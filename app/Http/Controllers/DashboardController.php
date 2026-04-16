<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Client;
use App\Models\Room;
use App\Models\Payment;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private const CACHE_TTL_STATS = 3600;
    private const CACHE_TTL_REVENUE = 1800;
    private const CACHE_TTL_OCCUPANCY = 1800;
    private const MAX_RECENT_RECORDS = 10;

    public function index(): View
    {
        Cache::forget('dashboard:stats:' . auth()->id());
        Cache::forget('dashboard:revenue_today:' . today()->format('Y-m-d'));
        Cache::forget('dashboard:occupancy_rate');

        $stats = $this->getStatsData();
        $recent_reservations = $this->getRecentReservations();
        $revenue_today = $this->getRevenueToday();
        $occupancy_rate = $this->getOccupancyRate();

        return view('dashboard', compact('stats', 'recent_reservations', 'revenue_today', 'occupancy_rate'));
    }

    private function getStatsData(): array
    {
        return Cache::remember('dashboard:stats:' . auth()->id(), self::CACHE_TTL_STATS, function () {
            try {
                $totalRevenue = Payment::where('estado', 'completado')->sum('monto');
            } catch (\Exception $e) {
                try {
                    $totalRevenue = Payment::where('status', 'completed')->sum('amount');
                } catch (\Exception $e) {
                    $totalRevenue = 0;
                }
            }

            return [
                'total_reservations' => Reservation::count(),
                'total_clients' => Client::count(),
                'total_rooms' => Room::count(),
                'total_revenue' => (float) $totalRevenue,
                'pending_reservations' => Reservation::where('estado', 'pendiente')->count(),
                'occupied_rooms' => Room::where('is_active', 0)->count(),
            ];
        });
    }

    private function getRecentReservations()
    {
        return Reservation::with(['client:id,nombre,email', 'room:id,numero,tipo'])
            ->select(['id', 'client_id', 'room_id', 'fecha_inicio', 'fecha_fin', 'estado', 'created_at'])
            ->where('created_at', '>=', now()->subDays(30))
            ->latest('created_at')
            ->limit(self::MAX_RECENT_RECORDS)
            ->get();
    }

    private function getRevenueToday(): float
    {
        return (float) Cache::remember('dashboard:revenue_today:' . today()->format('Y-m-d'), self::CACHE_TTL_REVENUE, function () {
            try {
                return Payment::where('estado', 'completado')
                    ->whereDate('created_at', today())
                    ->sum('monto');
            } catch (\Exception $e) {
                try {
                    return Payment::where('status', 'completed')
                        ->whereDate('created_at', today())
                        ->sum('amount');
                } catch (\Exception $e) {
                    return 0;
                }
            }
        });
    }

    private function getOccupancyRate(): float
    {
        return Cache::remember('dashboard:occupancy_rate', self::CACHE_TTL_OCCUPANCY, function () {
            $totalRooms = Room::count();

            if ($totalRooms === 0) {
                return 0.0;
            }

            $occupiedRooms = Room::where('is_active', 0)->count();
            return round(($occupiedRooms / $totalRooms) * 100, 2);
        });
    }
}