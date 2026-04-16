<?php

namespace App\Services;

use App\Models\Report;
use App\Models\Reservation;
use App\Models\Payment;
use App\Models\Client;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ReportGeneratorService
{
    public function generateRevenueReport(Report $report): void
    {
        $startDate = Carbon::parse($report->start_date);
        $endDate = Carbon::parse($report->end_date);

        $payments = Payment::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $totalRevenue = $payments->sum('amount');
        $totalTransactions = $payments->count();

        $dailyRevenue = $this->groupByDate($payments, 'created_at');

        $paymentMethods = $payments->groupBy('method')
            ->map(fn($group) => [
                'count' => $group->count(),
                'total' => $group->sum('amount'),
                'percentage' => round(($group->sum('amount') / $totalRevenue) * 100, 2),
            ]);

        $data = [
            'total_revenue' => $totalRevenue,
            'total_transactions' => $totalTransactions,
            'average_transaction' => $totalTransactions > 0 ? round($totalRevenue / $totalTransactions, 2) : 0,
            'daily_revenue' => $dailyRevenue,
            'payment_methods' => $paymentMethods,
        ];

        $summary = [
            'period' => $startDate->format('Y-m-d') . ' to ' . $endDate->format('Y-m-d'),
            'total_revenue' => $totalRevenue,
            'transactions_count' => $totalTransactions,
            'most_used_method' => $paymentMethods->sortByDesc('total')->keys()->first(),
        ];

        $report->markAsGenerated($data, $summary);
        $report->update(['total_amount' => $totalRevenue, 'record_count' => $totalTransactions]);
    }

    public function generateOccupancyReport(Report $report): void
    {
        $startDate = Carbon::parse($report->start_date);
        $endDate = Carbon::parse($report->end_date);

        $totalDays = $endDate->diffInDays($startDate);
        $totalRooms = Room::where('is_active', true)->count();
        $totalCapacity = $totalRooms * $totalDays;

        $reservations = Reservation::where('status', '!=', 'cancelled')
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('check_in', [$startDate, $endDate])
                    ->orWhereBetween('check_out', [$startDate, $endDate]);
            })
            ->get();

        $occupiedDays = 0;
        foreach ($reservations as $reservation) {
            $start = max($reservation->check_in, $startDate);
            $end = min($reservation->check_out, $endDate);
            $occupiedDays += $end->diffInDays($start);
        }

        $occupancyRate = $totalCapacity > 0 ? round(($occupiedDays / $totalCapacity) * 100, 2) : 0;

        $data = [
            'total_rooms' => $totalRooms,
            'total_days' => $totalDays,
            'occupied_days' => $occupiedDays,
            'occupancy_rate' => $occupancyRate,
            'total_reservations' => $reservations->count(),
            'average_occupancy_per_room' => $totalRooms > 0 ? round($occupiedDays / $totalRooms, 2) : 0,
        ];

        $summary = [
            'period' => $startDate->format('Y-m-d') . ' to ' . $endDate->format('Y-m-d'),
            'occupancy_percentage' => $occupancyRate,
            'total_reservations' => $reservations->count(),
        ];

        $report->markAsGenerated($data, $summary);
    }

    public function generateClientReport(Report $report): void
    {
        $startDate = Carbon::parse($report->start_date);
        $endDate = Carbon::parse($report->end_date);

        $clients = Client::whereHas('reservations', function ($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate]);
        })->with(['reservations' => function ($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate]);
        }])->get();

        $totalClients = $clients->count();
        $totalReservations = $clients->sum(fn($c) => $c->reservations->count());
        $repeatClients = $clients->filter(fn($c) => $c->reservations->count() > 1)->count();

        $data = [
            'total_clients' => $totalClients,
            'total_reservations' => $totalReservations,
            'repeat_clients' => $repeatClients,
            'new_clients' => $totalClients - $repeatClients,
            'average_reservations_per_client' => $totalClients > 0 ? round($totalReservations / $totalClients, 2) : 0,
        ];

        $summary = [
            'period' => $startDate->format('Y-m-d') . ' to ' . $endDate->format('Y-m-d'),
            'new_clients_count' => $totalClients - $repeatClients,
            'repeat_clients_count' => $repeatClients,
        ];

        $report->markAsGenerated($data, $summary);
    }

    private function groupByDate($collection, $dateField)
    {
        return $collection->groupBy(function ($item) use ($dateField) {
            return $item->{$dateField}->format('Y-m-d');
        })->map(fn($group) => $group->sum('amount'))->toArray();
    }
}
