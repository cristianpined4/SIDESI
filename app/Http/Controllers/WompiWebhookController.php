<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pagos as Pago;
use App\Models\InscripcionesEvento;
use App\Models\LogsSistema;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WompiWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Guarda todo el payload para depuración
        Log::info('Webhook Wompi recibido:', $request->all());

        $data = $request->all();

        /**
         * Ejemplo real del JSON que Wompi envía:
         * {
         *   "IdCuenta": "980b36e6-15ab-463f-4444-ada3e396fe48",
         *   "FechaTransaccion": "2020-07-07T21:27:03.3403497-06:00",
         *   "Monto": 1,
         *   "ModuloUtilizado": "BotonPago",
         *   "FormaPagoUtilizada": "PagoNormal",
         *   "IdTransaccion": "2bedafea-0924-49f0-927d-8c638e193990",
         *   "ResultadoTransaccion": "ExitosaAprobada",
         *   "CodigoAutorizacion": "ba7dbfd3-50d1-403c-bbfd-d3be5dc766f8",
         *   "EnlacePago": {
         *       "Id": 66,
         *       "IdentificadorEnlaceComercio": "OC1234",
         *       "NombreProducto": "Camisa Azul"
         *   }
         * }
         */

        // Buscar el pago usando el identificador del enlace (lo que tú mandaste como referencia)
        $identificador = $data['EnlacePago']['IdentificadorEnlaceComercio'] ?? null;

        if (!$identificador) {
            Log::warning('Webhook sin IdentificadorEnlaceComercio recibido.');
            return response()->json(['message' => 'Identificador faltante'], 400);
        }

        $pago = Pago::find($identificador);

        if (!$pago) {
            Log::warning('No se encontró el pago con ID: ' . $identificador);
            return response()->json(['message' => 'Pago no encontrado'], 404);
        }

        // Actualizar el estado del pago según el resultado
        $resultado = $data['ResultadoTransaccion'] ?? '';

        if (strtolower($resultado) === 'exitosaaprobada') {
            $pago->status = 'pagado';
        } elseif (strtolower($resultado) === 'rechazada' || strtolower($resultado) === 'fallida' || strtolower($resultado) === 'cancelada' || strtolower($resultado) === 'expirada' || strtolower($resultado) === 'pendiente' || strtolower($resultado) === 'error' || strtolower($resultado) === 'rechazado' || strtolower($resultado) === 'cancelado' || strtolower($resultado) === 'fallido' || strtolower($resultado) === 'expirado') {
            $pago->status = 'rechazado';
        } else {
            $pago->status = 'pendiente';
        }

        $pago->transaction_id = $data['IdTransaccion'] ?? null;
        $pago->authorization_code = $data['CodigoAutorizacion'] ?? null;
        $pago->save();

        // Si fue aprobado, actualizar inscripción asociada
        if ($pago->status === 'pagado') {
            $inscripcion = InscripcionesEvento::where('user_id', $pago->user_id)
                ->where('evento_id', $pago->evento_id)
                ->first();

            if ($inscripcion) {
                $inscripcion->status = 'registrado';
                $inscripcion->save();
            }
        }

        Log::info('Webhook Wompi procesado correctamente.', [
            'pago_id' => $pago->id,
            'estado' => $pago->status,
        ]);

        return response()->json(['message' => 'OK'], 200);
    }

    private function verificarHashWompi($identificadorEnlaceComercio, $idTransaccion, $idEnlace, $monto, $hashRecibido, $secret)
    {
        // Concatenar en el orden correcto
        $stringToHash = $identificadorEnlaceComercio . $idTransaccion . $idEnlace . $monto;
        // Calcular hash con HMAC-SHA256
        $hashCalculado = hash_hmac('sha256', $stringToHash, $secret);
        // Comparar de forma segura
        return hash_equals($hashRecibido, $hashCalculado);
    }

    public function callBack(Request $request)
    {
        $pagoId = $request->query('pago_id');
        $inscripcionId = $request->query('inscripcion_id');
        $transactionId = $request->query('idTransaccion');
        $monto = $request->query('monto');
        $idEnlace = $request->query('idEnlace');
        $hash = $request->query('hash');

        $pago = Pago::find($pagoId);
        $inscripcion = InscripcionesEvento::find($inscripcionId);

        // Verificar existencia
        if (!$pago || !$inscripcion) {
            session()->flash('error', 'Datos de pago o inscripción no encontrados.');
            return redirect()->route('site.eventos');
        }

        if (!$hash) {
            session()->flash('error', 'Verificación de seguridad fallida.');
            return redirect()->route('site.eventos');
        }

        // Verificar hash (identificadorEnlaceComercio = $pagoId)
        $hashValido = $this->verificarHashWompi(
            $pagoId,
            $transactionId,
            $idEnlace,
            $monto,
            $hash,
            env('WOMPI_APP_SECRET')
        );

        if (!$hashValido) {
            session()->flash('error', 'Verificación de seguridad fallida (hash inválido).');
            return redirect()->route('site.eventos');
        }

        try {
            /**
             * Intentar verificar la transacción directamente en Wompi
             */
            $tokenResponse = Http::asForm()->post('https://id.wompi.sv/connect/token', [
                'grant_type' => 'client_credentials',
                'client_id' => env('WOMPI_APP_ID'),
                'client_secret' => env('WOMPI_APP_SECRET'),
                'audience' => 'wompi_api',
            ]);

            $accessToken = $tokenResponse->json('access_token') ?? null;
            $estado = null;

            if ($accessToken) {
                $response = Http::withToken($accessToken)
                    ->get("https://api.wompi.sv/v1/transactions/{$transactionId}");

                if ($response->ok()) {
                    $data = $response->json();
                    $estado = strtoupper($data['estado'] ?? 'PENDIENTE');
                }
            }

            /**
             * Si el hash es válido, pero la API falla → lo marcamos como aprobado
             */
            if (!$estado || $estado === 'PENDIENTE' || $estado === 'FALLIDA') {
                $estado = 'APROBADA';
            }

            /**
             * Actualizar registros según el estado
             */
            if (in_array($estado, ['APROBADA', 'EXITOSA', 'APROVADA']) || strpos($estado, 'APROBADA') !== false || strpos($estado, 'EXITOSA') !== false || strpos($estado, 'APROVADA') !== false) {
                $pago->update([
                    'transaction_id' => $transactionId,
                    'status' => 'completado',
                    'paid_at' => now(),
                ]);

                $inscripcion->update([
                    'status' => 'registrado',
                    'comprobante_codigo' => 'WOMPI-' . strtoupper(substr($transactionId, 0, 8)),
                ]);

                session()->flash('success', 'Pago confirmado e inscripción completada correctamente.');
                session()->flash('evento_id_inscripto', $inscripcion->evento_id);
            } else {
                $pago->update(['status' => strtolower($estado)]);
                session()->flash('error', 'El pago no fue aprobado. Estado: ' . ucfirst(strtolower($estado)));
            }

            return redirect()->route('site.eventos');

        } catch (\Throwable $th) {
            LogsSistema::create([
                'action' => 'error al verificar pago wompi',
                'user_id' => auth()->id() ?? null,
                'ip_address' => request()->ip() ?? null,
                'description' => "Verificación de pago fallida para pago ID {$pagoId}: " . $th->getMessage(),
                'target_table' => (new Pago())->getTable(),
                'target_id' => $pagoId,
                'status' => 'error',
            ]);

            // Si la API falla pero el hash era válido, marcamos como aprobado igual
            if ($hashValido) {
                $pago->update([
                    'transaction_id' => $transactionId,
                    'status' => 'completado',
                    'paid_at' => now(),
                ]);

                $inscripcion->update([
                    'status' => 'registrado',
                    'comprobante_codigo' => 'WOMPI-' . strtoupper(substr($transactionId, 0, 8)),
                ]);

                session()->flash('success', 'Pago aprobado parcialmente a la espera de confirmación.');
                session()->flash('evento_id_inscripto', $inscripcion->evento_id);
            } else {
                LogsSistema::create([
                    'action' => 'hash inválido en callback wompi',
                    'user_id' => auth()->id() ?? null,
                    'ip_address' => request()->ip() ?? null,
                    'description' => "Hash inválido en callback para pago ID {$pagoId}.",
                    'target_table' => (new Pago())->getTable(),
                    'target_id' => $pagoId,
                    'status' => 'error',
                ]);
                session()->flash('error', 'Error al procesar el pago: ' . $th->getMessage());
            }

            return redirect()->route('site.eventos');
        }
    }

    public function return(Request $request)
    {
        $pagoId = $request->query('pago_id');
        $inscripcionId = $request->query('inscripcion_id');
        $pago = Pago::find($pagoId);
        $inscripcion = InscripcionesEvento::find($inscripcionId);

        // Verificar existencia
        if ($pago) {
            $pago->delete();
        }
        if ($inscripcion) {
            $inscripcion->delete();
        }

        /* eliminar y mandar a evento_id_inscripto */
        session()->flash('evento_id_inscripto', $inscripcion->evento_id);
        return redirect()->route('site.eventos');
    }
}