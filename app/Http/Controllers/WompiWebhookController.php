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
        Log::info('Webhook Wompi recibido', ['payload' => $request->all()]);

        $headers = $request->headers->all();
        $rawBody = $request->getContent();
        $data = json_decode($rawBody, true);

        if (!$data) {
            Log::warning('Webhook recibido sin cuerpo JSON válido.');
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        $identificador = $data['EnlacePago']['IdentificadorEnlaceComercio'] ?? null;

        if (!$identificador) {
            Log::warning('Webhook sin IdentificadorEnlaceComercio.');
            return response()->json(['message' => 'Identificador faltante'], 400);
        }

        $pago = Pago::find($identificador);

        if (!$pago) {
            Log::warning("No se encontró el pago con ID {$identificador}");
            return response()->json(['message' => 'Pago no encontrado'], 404);
        }

        $clientSecret = env('WOMPI_APP_SECRET');
        $wompiHash = $request->header('Wompi-Hash');

        // Calcula hash local
        $sig = hash_hmac('sha256', $rawBody, $clientSecret);

        $validoPorEndpoint = false;

        if (!$wompiHash) {
            Log::warning('Webhook sin hash, intentando validación por endpoint...');

            try {
                $tokenResponse = Http::asForm()->post('https://id.wompi.sv/connect/token', [
                    'grant_type' => 'client_credentials',
                    'client_id' => env('WOMPI_APP_ID'),
                    'client_secret' => env('WOMPI_APP_SECRET'),
                    'audience' => 'wompi_api',
                ]);

                $accessToken = $tokenResponse->json('access_token') ?? null;

                if ($accessToken) {
                    $response = Http::withToken($accessToken)
                        ->get("https://api.wompi.sv/TransaccionCompra/{$data['IdTransaccion']}");

                    if ($response->ok()) {
                        $body = $response->json();
                        $validoPorEndpoint = $body['esReal'] && $body['esAprobada'];
                    }
                }
            } catch (\Throwable $e) {
                Log::error('Error validando por endpoint Wompi: ' . $e->getMessage());
            }
        }

        $totalComercio = $pago->monto;
        $totalWompi = $data['Monto'] ?? 0;

        if ($totalComercio == $totalWompi) {
            if ($sig === $wompiHash || $validoPorEndpoint) {
                $pago->update([
                    'status' => 'completado',
                    'transaction_id' => $data['IdTransaccion'] ?? null,
                    'paid_at' => now(),
                ]);

                InscripcionesEvento::where('user_id', $pago->user_id)
                    ->where('evento_id', $pago->evento_id)
                    ->update(['status' => 'registrado']);

                Log::info("Pago {$pago->id} confirmado por webhook.");

                return response()->json(['message' => 'OK'], 200);
            } else {
                Log::warning("Hash no válido para pago {$pago->id}");
                $pago->update(['status' => 'fallido']);
            }
        } else {
            Log::warning("Montos no coinciden para pago {$pago->id}. Comercio: {$totalComercio}, Wompi: {$totalWompi}");
            $pago->update(['status' => 'fallido']);
        }

        return response()->json(['message' => 'Processed'], 200);
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
        $idTransaccion = $request->query('idTransaccion');
        $idEnlace = $request->query('idEnlace');
        $monto = $request->query('monto');
        $hashRecibido = $request->query('hash');

        $pago = Pago::find($pagoId);
        $inscripcion = InscripcionesEvento::find($inscripcionId);

        if (!$pago || !$inscripcion) {
            session()->flash('error', 'Datos de pago o inscripción no encontrados.');
            return redirect()->route('site.eventos');
        }

        if (!$hashRecibido) {
            session()->flash('error', 'Falta verificación de seguridad.');
            return redirect()->route('site.eventos');
        }

        $hashValido = $this->verificarHashWompi(
            $pagoId,
            $idTransaccion,
            $idEnlace,
            $monto,
            $hashRecibido,
            env('WOMPI_APP_SECRET')
        );

        if (!$hashValido) {
            session()->flash('error', 'Verificación de seguridad fallida (hash inválido).');
            return redirect()->route('site.eventos');
        }

        try {
            // Validar en Wompi si realmente está aprobada
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
                    ->get("https://api.wompi.sv/TransaccionCompra/{$idTransaccion}");

                if ($response->ok()) {
                    $data = $response->json();
                    $estado = $data['esAprobada'] ? 'APROBADA' : 'RECHAZADA';
                }
            }

            if (!$estado) {
                $estado = 'APROBADA'; // fallback igual que WP
            }

            if ($estado === 'APROBADA') {
                $pago->update([
                    'transaction_id' => $idTransaccion,
                    'status' => 'completado',
                    'paid_at' => now(),
                ]);

                $inscripcion->update([
                    'status' => 'registrado',
                    'comprobante_codigo' => 'WOMPI-' . strtoupper(substr($idTransaccion, 0, 8)),
                ]);

                session()->flash('success', 'Pago confirmado correctamente e inscripción completada con éxito.');
                session()->flash('evento_id_inscripto', $inscripcion->evento_id);
            } else {
                $pago->update(['status' => 'rechazado']);
                session()->flash('error', 'El pago fue rechazado por Wompi.');
            }

            return redirect()->route('site.eventos');

        } catch (\Throwable $th) {
            Log::error('Error en callback Wompi: ' . $th->getMessage());

            if ($hashValido) {
                $pago->update([
                    'transaction_id' => $idTransaccion,
                    'status' => 'pendiente_confirmacion',
                ]);
                session()->flash('warning', 'Pago recibido, pendiente de verificación.');
            } else {
                session()->flash('error', 'Error de verificación de pago.');
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