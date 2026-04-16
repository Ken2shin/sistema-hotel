<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Models\Review;
use App\Models\RoomRating;
use App\Models\Client;
use App\Models\Room;
use App\Models\Reservation;
use Illuminate\Http\JsonResponse;

class ReviewController extends Controller
{
    public function store(StoreReviewRequest $request): JsonResponse
    {
        try {
            $clientId = $request->input('client_id');
            $roomId = $request->input('room_id');
            $reservationId = $request->input('reservation_id');

            $client = Client::find($clientId);
            $room = Room::find($roomId);
            $reservation = Reservation::find($reservationId);

            if (!$client || !$room || !$reservation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente, habitación o reserva no encontrados'
                ], 404);
            }

            $reviewExistente = Review::where('reservation_id', $reservationId)->exists();
            if ($reviewExistente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existe una reseña para esta reserva'
                ], 400);
            }

            $calificacion = $request->input('calificacion');
            if ($calificacion < 1 || $calificacion > 5) {
                return response()->json([
                    'success' => false,
                    'message' => 'La calificación debe estar entre 1 y 5'
                ], 400);
            }

            $review = Review::create([
                'client_id' => $clientId,
                'room_id' => $roomId,
                'reservation_id' => $reservationId,
                'calificacion' => $calificacion,
                'comentario' => $request->input('comentario'),
                'is_verified' => true,
            ]);

            $this->actualizarRating($roomId);

            return response()->json([
                'success' => true,
                'data' => $review,
                'message' => 'Reseña creada exitosamente'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la reseña'
            ], 500);
        }
    }

    private function actualizarRating(int $roomId): void
    {
        $reviews = Review::where('room_id', $roomId)
            ->where('is_verified', true)
            ->get();

        if ($reviews->isNotEmpty()) {
            $promedio = $reviews->avg('calificacion');
            $total = $reviews->count();

            RoomRating::updateOrCreate(
                ['room_id' => $roomId],
                ['promedio' => round($promedio, 2), 'total_resenas' => $total]
            );
        }
    }
}
