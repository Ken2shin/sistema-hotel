<?php

namespace App\Livewire;

use App\Models\Reservation;
use App\Models\Client;
use App\Models\Room;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class ReservationDashboard extends Component
{
    use WithPagination;

    public string $search = '';
    public string $estado = '';
    public string $fecha = '';
    public int $perPage = 25;
    
    public bool $showForm = false;
    public string $formMode = 'create';
    public ?int $editingId = null;
    
    public $client_id = null;
    public $room_id = null;
    public $check_in = null;
    public $check_out = null;
    public $status = 'pendiente'; 

    public array $servicios_disponibles = [];
    public array $servicios_seleccionados = [];

    protected $queryString = ['search', 'estado', 'fecha'];

    public function mount()
    {
        $this->check_in = now()->format('Y-m-d');
        $this->check_out = now()->addDays(1)->format('Y-m-d');
    }

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingEstado(): void { $this->resetPage(); }
    public function updatingFecha(): void { $this->resetPage(); }

    private function parseServicios($data)
    {
        if (empty($data)) return [];
        if (is_array($data)) return $data;

        $intentos = 0;
        while (is_string($data) && $intentos < 3) {
            $decoded = json_decode($data, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $data = $decoded;
            } else {
                break;
            }
            $intentos++;
        }
        return is_array($data) ? $data : [];
    }

    public function updatedRoomId($value)
    {
        if ($value) {
            $room = Room::find($value);
            $this->servicios_disponibles = $this->parseServicios($room->servicios);
        } else {
            $this->servicios_disponibles = [];
        }
        $this->servicios_seleccionados = [];
    }

    public function openCreateForm(): void
    {
        $this->resetForm();
        $this->formMode = 'create';
        $this->showForm = true;
    }

    public function openEditForm(int $id): void
    {
        $reservation = Reservation::find($id);
        if ($reservation) {
            $this->editingId = $id;
            $this->client_id = $reservation->client_id;
            $this->room_id = $reservation->room_id;
            $this->check_in = Carbon::parse($reservation->fecha_inicio)->format('Y-m-d');
            $this->check_out = Carbon::parse($reservation->fecha_fin)->format('Y-m-d');
            $this->status = $reservation->estado;
            
            $room = Room::find($reservation->room_id);
            $this->servicios_disponibles = $this->parseServicios($room->servicios ?? []);
            
            $notas = json_decode($reservation->notas ?? '[]', true);
            $this->servicios_seleccionados = is_array($notas) ? $notas : [];
            
            $this->formMode = 'edit';
            $this->showForm = true;
        }
    }

    public function save(): void
    {
        $this->validate([
            'client_id' => 'required|exists:clients,id',
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'status' => 'required|in:pendiente,confirmada,cancelada,completada',
        ]);

        $isOccupied = Reservation::where('room_id', $this->room_id)
            ->where('estado', '!=', 'cancelada')
            ->where(function ($q) {
                $q->whereBetween('fecha_inicio', [$this->check_in, $this->check_out])
                  ->orWhereBetween('fecha_fin', [$this->check_in, $this->check_out])
                  ->orWhere(function ($q2) {
                      $q2->where('fecha_inicio', '<=', $this->check_in)
                         ->where('fecha_fin', '>=', $this->check_out);
                  });
            });

        if ($this->editingId) {
            $isOccupied->where('id', '!=', $this->editingId);
        }

        if ($isOccupied->exists()) {
            session()->flash('error', '¡Error! La habitación ya se encuentra ocupada o reservada para esas fechas.');
            return;
        }

        try {
            $checkIn = Carbon::parse($this->check_in);
            $checkOut = Carbon::parse($this->check_out);
            $cantidad_noches = $checkIn->diffInDays($checkOut);
            if($cantidad_noches < 1) $cantidad_noches = 1;

            $room = Room::find($this->room_id);
            $precio_total = $cantidad_noches * ($room->precio_noche ?? 0);
            $servicios_json = json_encode($this->servicios_seleccionados);

            $reservationData = [
                'client_id' => $this->client_id,
                'room_id' => $this->room_id,
                'fecha_inicio' => $this->check_in,
                'fecha_fin' => $this->check_out,
                'cantidad_noches' => $cantidad_noches,
                'precio_total' => $precio_total,
                'notas' => $servicios_json,
                'estado' => $this->status,
                'created_by' => Auth::id() ?? 1,
            ];

            if ($this->formMode === 'create') {
                $reservation = Reservation::create($reservationData);
                
                if ($reservation->estado === 'confirmada') {
                    $this->updateRoomStatus($reservation->room_id);
                }
                
                session()->flash('message', 'Reserva creada exitosamente.');
            } else {
                $reservation = Reservation::find($this->editingId);
                if ($reservation) {
                    $oldStatus = $reservation->estado;
                    
                    $reservation->update($reservationData);
                    
                    if ($this->status === 'confirmada' && $oldStatus !== 'confirmada') {
                        $this->updateRoomStatus($reservation->room_id);
                    } elseif ($this->status !== 'confirmada' && $oldStatus === 'confirmada') {
                        $this->restoreRoomStatus($reservation->room_id);
                    }
                    
                    session()->flash('message', 'Reserva actualizada exitosamente.');
                }
            }
            $this->resetForm();
            $this->dispatch('reservation-saved');
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }
    
    public function updateReservationStatus(int $id, string $newStatus): void
    {
        if (!in_array($newStatus, ['pendiente', 'confirmada', 'completada', 'cancelada'])) {
            return;
        }

        $reservation = Reservation::find($id);
        if (!$reservation) {
            return;
        }

        $oldStatus = $reservation->estado;
        $reservation->update(['estado' => $newStatus]);

        if ($newStatus === 'confirmada' && $oldStatus !== 'confirmada') {
            $this->updateRoomStatus($reservation->room_id);
        } elseif ($newStatus !== 'confirmada' && $oldStatus === 'confirmada') {
            $this->restoreRoomStatus($reservation->room_id);
        }

        session()->flash('message', 'Estado de reserva actualizado.');
    }

    private function updateRoomStatus(int $roomId): void
    {
        $room = Room::find($roomId);
        if ($room) {
        }
    }

    private function restoreRoomStatus(int $roomId): void
    {
        $room = Room::find($roomId);
        if ($room) {
            $hasActiveReservation = Reservation::where('room_id', $roomId)
                ->where('estado', 'confirmada')
                ->exists();
            
            if (!$hasActiveReservation) {
            }
        }
    }

    public function cancelReservation(int $id): void
    {
        $reservation = Reservation::find($id);
        
        if ($reservation && $reservation->estado !== 'cancelada') {
            $oldStatus = $reservation->estado;
            
            $reservation->update(['estado' => 'cancelada']);
            
            if ($oldStatus === 'confirmada') {
                $this->restoreRoomStatus($reservation->room_id);
            }
            
            session()->flash('message', 'Reserva cancelada exitosamente.');
            $this->dispatch('reservation-cancelled', id: $id);
        }
    }

    public function resetForm(): void
    {
        $this->reset(['client_id', 'room_id', 'editingId', 'servicios_disponibles', 'servicios_seleccionados']);
        $this->check_in = now()->format('Y-m-d');
        $this->check_out = now()->addDays(1)->format('Y-m-d');
        $this->status = 'pendiente';
        $this->showForm = false;
        $this->resetValidation();
    }

    public function render()
    {
        $reservations = Reservation::with(['client:id,nombre,email,telefono', 'room:id,numero,tipo'])
            ->when($this->search, function ($query) {
                return $query->whereHas('client', function ($q) {
                    $q->where('nombre', 'like', "%{$this->search}%")
                      ->orWhere('email', 'like', "%{$this->search}%");
                })->orWhereHas('room', function($q) {
                    $q->where('numero', 'like', "%{$this->search}%");
                });
            })
            ->when($this->estado, function ($query) {
                return $query->where('estado', $this->estado);
            })
            ->when($this->fecha, function ($query) {
                return $query->whereDate('fecha_inicio', '<=', $this->fecha)
                             ->whereDate('fecha_fin', '>=', $this->fecha);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        $clients = Client::orderBy('nombre')->get(['id', 'nombre']);
        
        $allRooms = Room::where('is_active', true)->orderBy('numero')->get(['id', 'numero', 'tipo', 'precio_noche', 'servicios']);
        $availableRooms = [];
        $occupiedRooms = [];

        if ($this->check_in && $this->check_out) {
            foreach ($allRooms as $room) {
                $isOccupied = Reservation::where('room_id', $room->id)
                    ->where('estado', '!=', 'cancelada')
                    ->where(function ($q) {
                        $q->whereBetween('fecha_inicio', [$this->check_in, $this->check_out])
                          ->orWhereBetween('fecha_fin', [$this->check_in, $this->check_out])
                          ->orWhere(function ($q2) {
                              $q2->where('fecha_inicio', '<=', $this->check_in)
                                 ->where('fecha_fin', '>=', $this->check_out);
                          });
                    });

                if ($this->editingId) {
                    $isOccupied->where('id', '!=', $this->editingId);
                }

                if ($isOccupied->exists()) {
                    $occupiedRooms[] = $room;
                } else {
                    $availableRooms[] = $room;
                }
            }
        } else {
            $availableRooms = $allRooms->toArray();
        }

        return view('livewire.reservation-dashboard', [
            'reservations' => $reservations,
            'clients' => $clients,
            'availableRooms' => $availableRooms,
            'occupiedRooms' => $occupiedRooms,
        ]);
    }
}