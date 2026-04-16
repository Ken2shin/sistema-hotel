<?php

namespace App\Livewire;

use App\Models\Payment;
use App\Models\Reservation;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class PaymentManager extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $perPage = 10;
    
    public $showForm = false;
    public $reservation_id = '';
    public $monto = '';
    public $metodo_pago = '';
    public $descripcion = '';

    public function mount()
    {
        $this->metodo_pago = 'efectivo';
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function openCreateForm()
    {
        $this->reset(['reservation_id', 'monto', 'descripcion']);
        $this->metodo_pago = 'efectivo';
        $this->showForm = true;
        $this->resetValidation();
    }

    public function closeForm()
    {
        $this->showForm = false;
    }

    public function savePayment()
    {
        $this->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'monto' => 'required|numeric|min:0.01',
            'metodo_pago' => 'required|string',
            'descripcion' => 'nullable|string|max:255',
        ]);

        try {
            $reservation = Reservation::find($this->reservation_id);
            
            $numeroTransaccion = 'TRX-' . time() . '-' . strtoupper(substr(uniqid(), -5));

            Payment::create([
                'reservation_id' => $this->reservation_id,
                'monto' => $this->monto,
                'metodo_pago' => $this->metodo_pago,
                'numero_transaccion' => $numeroTransaccion,
                'estado' => 'completado',
                'fecha_pago' => now(),
                'descripcion' => $this->descripcion,
            ]);

            session()->flash('message', 'Pago registrado exitosamente. (TRX: ' . $numeroTransaccion . ')');
            
            $this->closeForm();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error al registrar el pago: ' . $e->getMessage());
        }
    }

    public function deletePayment($id)
    {
        Payment::find($id)?->delete();
        session()->flash('message', 'Pago eliminado del registro.');
    }

    public function markAsCompleted($id)
    {
        $payment = Payment::find($id);
        if($payment) {
            $payment->update(['estado' => 'completado']);
            session()->flash('message', 'Pago marcado como completado.');
        }
    }

    public function render()
    {
        $query = Payment::with(['reservation.client']);

        if (!empty($this->search)) {
            $query->whereHas('reservation.client', function($q) {
                $q->where('nombre', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            })
            ->orWhere('numero_transaccion', 'like', "%{$this->search}%");
        }

        if (!empty($this->status)) {
            $query->where('estado', $this->status);
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate($this->perPage);
        
        $pendingReservations = Reservation::with('client')
            ->whereIn('estado', ['pendiente', 'confirmada'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.payment-manager', [
            'payments' => $payments,
            'pendingReservations' => $pendingReservations,
        ]);
    }
}