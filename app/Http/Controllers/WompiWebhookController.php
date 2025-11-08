<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Pagos as Pago;
use App\Models\InscripcionesEvento;

class WompiWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // 📋 Guarda todo el payload para depuración
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

        // 📌 Buscar el pago usando el identificador del enlace (lo que tú mandaste como referencia)
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

        // 🔄 Actualizar el estado del pago según el resultado
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

        // 🎟 Si fue aprobado, actualizar inscripción asociada
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
}