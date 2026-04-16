<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Reservation;
use App\Models\Client;
use App\Models\Room;
use App\Models\Payment;

class DashboardStats extends Component
{
    public $stats = [];
    public $recent_reservations = [];
    public $revenue_today = 0;
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
        $this->stats = [
            'total_reservations' => Reservation::count(),
            'total_clients' => Client::count(),
            'total_rooms' => Room::count(),
            'total_revenue' => (float) (Payment::where('status', 'completed')->sum('amount') ?? 0),
            'pending_reservations' => Reservation::where('status', 'pending')->count(),
            'occupied_rooms' => Room::where('status', 'occupied')->count(),
        ];

        $this->recent_reservations = Reservation::with(['client:id,name,email', 'room:id,number,type'])
            ->select(['id', 'client_id', 'room_id', 'check_in', 'check_out', 'status', 'created_at'])
            ->where('created_at', '>=', now()->subDays(30))
            ->latest('created_at')
            ->limit(10)
            ->get();

        $this->revenue_today = (float) (Payment::where('status', 'completed')
            ->whereDate('created_at', today())
            ->sum('amount') ?? 0);

        $totalRooms = $this->stats['total_rooms'] ?? 0;
        if ($totalRooms === 0) {
            $this->occupancy_rate = 0.0;
        } else {
            $this->occupancy_rate = round(($this->stats['occupied_rooms'] / $totalRooms) * 100, 2);
        }
    }

    public function render()
    {
        return view('livewire.dashboard-stats');
    }
}
