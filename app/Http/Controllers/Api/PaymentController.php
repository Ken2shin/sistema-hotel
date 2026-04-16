<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRequest;
use App\Models\Payment;
use App\Models\Reservation;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    public function store(StorePaymentRequest $request): JsonResponse
    {
        try {
            $reservationId = $request->input('reservation_id');
            $monto = $request->input('monto');
            $metodoPago = $request->input('metodo_pago');

            $reservation = Reservation::find($reservationId);

            if (!$reservation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Reserva no encontrada'
                ], 404);
            }

            if ($monto > $reservation->precio_total) {
                return response()->json([
                    'success' => false,
                    'message' => 'El monto excede el precio total de la reserva'
                ], 400);
            }

            $numeroTransaccion = 'TRX-' . time() . '-' . uniqid();

            $payment = Payment::create([
                'reservation_id' => $reservationId,
                'monto' => $monto,
                'metodo_pago' => $metodoPago,
                'numero_transaccion' => $numeroTransaccion,
                'estado' => 'completado',
                'fecha_pago' => now(),
                'descripcion' => $request->input('descripcion'),
            ]);

            if ($monto >= $reservation->precio_total) {
                $reservation->update(['estado' => 'pagada']);
            }

            return response()->json([
                'success' => true,
                'data' => $payment,
                'message' => 'Pago procesado exitosamente'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el pago'
            ], 500);
        }
    }
}
