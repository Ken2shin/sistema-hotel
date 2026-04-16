<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Models\Reservation;
use App\Models\Room;
use App\Services\ReservationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ReservationController extends Controller
{
    public function __construct(private ReservationService $reservationService) {}

    public function index(): JsonResponse
    {
        try {
            $reservations = Reservation::with(['client', 'room'])
                ->orderBy('check_in', 'desc')
                ->paginate(20);

            return response()->json([
                'data' => $reservations->items(),
                'pagination' => [
                    'current_page' => $reservations->currentPage(),
                    'total' => $reservations->total(),
                    'per_page' => $reservations->perPage(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Reservation index failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to fetch reservations'], 500);
        }
    }

    public function store(CreateReservationRequest $request): JsonResponse
    {
        try {
            $reservation = $this->reservationService->createReservation($request->validated());

            Log::info('Reservation created', ['reservation_id' => $reservation->id, 'user_id' => auth()->id()]);

            return response()->json([
                'data' => $reservation->load(['client', 'room']),
                'message' => 'Reserva creada exitosamente',
            ], 201);
        } catch (\Exception $e) {
            Log::warning('Reservation creation failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function show(Reservation $reservation): JsonResponse
    {
        return response()->json(['data' => $reservation->load(['client', 'room', 'payments'])]);
    }

    public function update(UpdateReservationRequest $request, Reservation $reservation): JsonResponse
    {
        try {
            $updated = $this->reservationService->updateReservation($reservation, $request->validated());

            Log::info('Reservation updated', ['reservation_id' => $reservation->id]);

            return response()->json(['data' => $updated->load(['client', 'room'])]);
        } catch (\Exception $e) {
            Log::warning('Reservation update failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function cancel(Reservation $reservation): JsonResponse
    {
        try {
            $this->reservationService->cancelReservation($reservation, request()->input('reason'));

            Log::info('Reservation cancelled', ['reservation_id' => $reservation->id]);

            return response()->json(['message' => 'Reserva cancelada']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al cancelar'], 500);
        }
    }

    public function clientReservations(int $clientId): JsonResponse
    {
        try {
            $reservations = Reservation::where('client_id', $clientId)
                ->with(['room', 'payments'])
                ->orderBy('check_in', 'desc')
                ->get();

            return response()->json(['data' => $reservations]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener reservas'], 500);
        }
    }

    public function available(): JsonResponse
    {
        try {
            $checkIn = request()->input('check_in');
            $checkOut = request()->input('check_out');

            if (!$checkIn || !$checkOut) {
                return response()->json(['error' => 'Fechas requeridas'], 400);
            }

            $rooms = $this->reservationService->getAvailableRooms($checkIn, $checkOut);

            return response()->json(['data' => $rooms]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener disponibilidad'], 500);
        }
    }
}
