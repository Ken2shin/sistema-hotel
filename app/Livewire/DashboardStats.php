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
    
    // Métricas exclusivas del día actual (Se reinician a 0 a la medianoche)
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
        // 1. OBTENER EL RANGO EXACTO DEL DÍA ACTUAL
        // Esto previene fallos en producción garantizando que busque 
        // desde las 00:00:00 hasta las 23:59:59 de hoy (Timezone de la APP).
        $startOfToday = now()->startOfDay();
        $endOfToday = now()->endOfDay();

        // 2. MÉTRICAS GLOBALES DEL HOTEL (No dependen del día)
        $this->stats = [
            'total_rooms' => Room::count(),
            'occupied_rooms' => Room::where('status', 'occupied')->count(),
            'pending_reservations' => Reservation::where('status', 'pending')->count(),
        ];

        // 3. MÉTRICAS DIARIAS (Se reinician a 0 cada día automáticamente)
        $this->clients_today = Client::whereBetween('created_at', [$startOfToday, $endOfToday])->count();
        $this->reservations_today = Reservation::whereBetween('created_at', [$startOfToday, $endOfToday])->count();
        $this->revenue_today = (float) Payment::where('status', 'completed')
            ->whereBetween('created_at', [$startOfToday, $endOfToday])
            ->sum('amount');

        // 4. CÁLCULO DE OCUPACIÓN SEGURO
        $totalRooms = $this->stats['total_rooms'] ?? 0;
        $this->occupancy_rate = $totalRooms > 0 
            ? round(($this->stats['occupied_rooms'] / $totalRooms) * 100, 2) 
            : 0.0;

        // 5. RESERVAS RECIENTES (Optimizado para no saturar memoria)
        $this->recent_reservations = Reservation::with(['client:id,name', 'room:id,number'])
            ->select(['id', 'client_id', 'room_id', 'check_in', 'status', 'created_at'])
            ->latest('created_at') // Las más nuevas primero
            ->limit(8)             // Limitamos a 8 para mantener limpio el diseño
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard-stats');
    }
}