<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Reservation;
use App\Models\Client;
use App\Models\Room;
use App\Models\Payment;
use Carbon\Carbon;

class DashboardStats extends Component
{
    public $stats = [];
    public $recent_reservations = [];
    
    // Métricas del día
    public $revenue_today = 0;
    public $clients_today = 0;
    public $reservations_today = 0;
    public $occupancy_rate = 0;

    #[On('reservation-saved')]
    #[On('room-updated')]
    #[On('payment-completed')]
    #[On('image-uploaded')]
    public function refreshStats()
    {
        $this->loadStats();
    }

    public function mount()
    {
        $this->loadStats();
    }

    private function loadStats()
    {
        $today = Carbon::today(); // 00:00:00 del día actual

        $this->stats = [
            'total_reservations' => Reservation::count(),
            'total_clients' => Client::count(),
            'total_rooms' => Room::count(),
            'total_revenue' => (float) (Payment::where('status', 'completed')->sum('amount') ?? 0),
            'pending_reservations' => Reservation::where('status', 'pending')->count(),
            'occupied_rooms' => Room::where('status', 'occupied')->count(),
        ];

        // Métricas diarias (Today's Metrics)
        $this->clients_today = Client::whereDate('created_at', $today)->count();
        $this->reservations_today = Reservation::whereDate('created_at', $today)->count();
        $this->revenue_today = (float) (Payment::where('status', 'completed')
            ->whereDate('created_at', $today)
            ->sum('amount') ?? 0);

        // Ocupación calculada de forma segura
        $totalRooms = $this->stats['total_rooms'] ?? 0;
        $this->occupancy_rate = $totalRooms > 0 
            ? round(($this->stats['occupied_rooms'] / $totalRooms) * 100, 2) 
            : 0.0;

        $this->recent_reservations = Reservation::with(['client:id,name,email', 'room:id,number,type'])
            ->select(['id', 'client_id', 'room_id', 'check_in', 'check_out', 'status', 'created_at'])
            ->where('created_at', '>=', now()->subDays(30))
            ->latest('created_at')
            ->limit(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard-stats');
    }
}