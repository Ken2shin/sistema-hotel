<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\Room;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class ReservationService
{
    public function createReservation(array $data): Reservation
    {
        $room = Room::findOrFail($data['room_id']);
        $client = Client::findOrFail($data['client_id']);

        if (!$this->isRoomAvailable($room, $data['check_in'], $data['check_out'])) {
            throw new \Exception('Habitación no disponible para las fechas seleccionadas.');
        }

        $nights = $this->calculateNights($data['check_in'], $data['check_out']);
        $totalPrice = $this->calculatePrice($room, $data['check_in'], $data['check_out'], $nights);

        return Reservation::create([
            'room_id' => $data['room_id'],
            'client_id' => $data['client_id'],
            'check_in' => $data['check_in'],
            'check_out' => $data['check_out'],
            'number_of_guests' => $data['number_of_guests'],
            'special_requests' => $data['special_requests'] ?? null,
            'breakfast_included' => $data['breakfast_included'] ?? false,
            'parking' => $data['parking'] ?? false,
            'total_price' => $totalPrice,
            'nights' => $nights,
            'status' => 'confirmed',
        ]);
    }

    public function updateReservation(Reservation $reservation, array $data): Reservation
    {
        if (isset($data['check_in']) || isset($data['check_out'])) {
            $checkIn = $data['check_in'] ?? $reservation->check_in;
            $checkOut = $data['check_out'] ?? $reservation->check_out;

            if (!$this->isRoomAvailable($reservation->room, $checkIn, $checkOut, $reservation->id)) {
                throw new \Exception('Habitación no disponible para las nuevas fechas.');
            }

            $nights = $this->calculateNights($checkIn, $checkOut);
            $data['total_price'] = $this->calculatePrice($reservation->room, $checkIn, $checkOut, $nights);
            $data['nights'] = $nights;
        }

        $reservation->update($data);
        return $reservation;
    }

    public function cancelReservation(Reservation $reservation, string $reason = null): void
    {
        $reservation->update([
            'status' => 'cancelled',
            'cancellation_reason' => $reason,
            'cancelled_at' => now(),
        ]);
    }

    public function getAvailableRooms(string $checkIn, string $checkOut): Collection
    {
        $checkIn = Carbon::parse($checkIn);
        $checkOut = Carbon::parse($checkOut);

        return Room::where('is_active', true)
            ->whereDoesntHave('reservations', function ($query) use ($checkIn, $checkOut) {
                $query->where('status', '!=', 'cancelled')
                    ->where(function ($q) use ($checkIn, $checkOut) {
                        $q->whereBetween('check_in', [$checkIn, $checkOut])
                            ->orWhereBetween('check_out', [$checkIn, $checkOut])
                            ->orWhere(function ($q2) use ($checkIn, $checkOut) {
                                $q2->where('check_in', '<=', $checkIn)
                                    ->where('check_out', '>=', $checkOut);
                            });
                    });
            })
            ->get();
    }

    public function isRoomAvailable(Room $room, string $checkIn, string $checkOut, ?int $excludeReservationId = null): bool
    {
        $checkIn = Carbon::parse($checkIn);
        $checkOut = Carbon::parse($checkOut);

        $query = $room->reservations()
            ->where('status', '!=', 'cancelled')
            ->where(function ($q) use ($checkIn, $checkOut) {
                $q->whereBetween('check_in', [$checkIn, $checkOut])
                    ->orWhereBetween('check_out', [$checkIn, $checkOut])
                    ->orWhere(function ($q2) use ($checkIn, $checkOut) {
                        $q2->where('check_in', '<=', $checkIn)
                            ->where('check_out', '>=', $checkOut);
                    });
            });

        if ($excludeReservationId) {
            $query->where('id', '!=', $excludeReservationId);
        }

        return !$query->exists();
    }

    private function calculateNights(string $checkIn, string $checkOut): int
    {
        return Carbon::parse($checkOut)->diffInDays(Carbon::parse($checkIn));
    }

    private function calculatePrice(Room $room, string $checkIn, string $checkOut, int $nights): float
    {
        $startDate = Carbon::parse($checkIn);
        $endDate = Carbon::parse($checkOut);
        $totalPrice = 0;

        while ($startDate < $endDate) {
            $isWeekend = $startDate->isWeekend();
            $price = $isWeekend ? $room->precio_fin_semana : $room->precio_noche;
            $totalPrice += $price;
            $startDate->addDay();
        }

        return $totalPrice;
    }
}
