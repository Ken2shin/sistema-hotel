<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Client;
use App\Models\Room;

class GlobalHeader extends Component
{
    public $search = '';
    public $searchResults = [];
    public $unreadNotifications = 3; // Simulación: Puedes conectarlo a auth()->user()->unreadNotifications()

    public function updatedSearch()
    {
        if (strlen($this->search) < 2) {
            $this->searchResults = [];
            return;
        }

        $results = collect();

        // 1. Buscar en Clientes
        if (class_exists(Client::class)) {
            $clients = Client::where('nombre', 'ilike', '%' . $this->search . '%')
                ->orWhere('email', 'ilike', '%' . $this->search . '%')
                ->limit(3)
                ->get()
                ->map(function($c) {
                    return [
                        'type' => 'Cliente',
                        'title' => $c->nombre,
                        'subtitle' => $c->email,
                        'icon' => 'fa-user',
                        'icon_color' => 'text-blue-600',
                        'icon_bg' => 'bg-blue-100',
                        'url' => route('admin.clients') // Ajusta si tienes vista de perfil
                    ];
                });
            $results = $results->merge($clients);
        }

        // 2. Buscar en Habitaciones (Opcional, si tienes el modelo)
        if (class_exists(Room::class)) {
            $rooms = Room::where('numero', 'ilike', '%' . $this->search . '%')
                ->limit(2)
                ->get()
                ->map(function($r) {
                    return [
                        'type' => 'Habitación',
                        'title' => 'Habitación ' . $r->numero,
                        'subtitle' => 'Estado: ' . $r->status,
                        'icon' => 'fa-bed',
                        'icon_color' => 'text-purple-600',
                        'icon_bg' => 'bg-purple-100',
                        'url' => route('admin.rooms') 
                    ];
                });
            $results = $results->merge($rooms);
        }

        $this->searchResults = $results->toArray();
    }

    public function clearNotifications()
    {
        $this->unreadNotifications = 0;
        // Aquí iría: auth()->user()->unreadNotifications->markAsRead();
    }

    public function render()
    {
        return view('livewire.global-header');
    }
}