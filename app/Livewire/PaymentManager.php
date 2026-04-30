<?php

namespace App\Livewire;

use App\Models\Payment;
use App\Models\Reservation; // Importamos el modelo Reservation para el select
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
    
    // Variables de UI
    public $showForm = false;

    // Variables del Formulario
    public $reservation_id = '';
    public $monto = '';
    public $metodo_pago = 'efectivo'; // valor por defecto
    public $estado_pago = 'completado'; // valor por defecto
    public $referencia = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
    ];

    protected $paginationTheme = 'tailwind';

    // Reglas de validación
    protected function rules()
    {
        return [
            'reservation_id' => 'required|exists:reservations,id',
            'monto' => 'required|numeric|min:0.01',
            'metodo_pago' => 'required|in:efectivo,tarjeta_credito,tarjeta_debito,transferencia',
            'estado_pago' => 'required|in:completado,pendiente,fallido',
            'referencia' => 'nullable|string|max:100',
        ];
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
        $this->resetForm();
        $this->showForm = true;
    }

    public function closeForm()
    {
        $this->showForm = false;
        $this->resetValidation();
    }

    public function resetForm()
    {
        $this->reset(['reservation_id', 'monto', 'referencia']);
        $this->metodo_pago = 'efectivo';
        $this->estado_pago = 'completado';
    }

    public function save()
    {
        $this->validate();

        try {
            Payment::create([
                'reservation_id' => $this->reservation_id,
                'monto' => $this->monto,
                'metodo_pago' => $this->metodo_pago,
                'estado' => $this->estado_pago,
                // Generamos un numero de transaccion automatico si el usuario no pone referencia
                'numero_transaccion' => $this->referencia ?: 'TRX-' . strtoupper(uniqid()),
            ]);

            session()->flash('message', 'La transacción ha sido procesada y registrada exitosamente.');
            $this->closeForm();
            
            // Si el dashboard está escuchando este evento, se actualizará
            $this->dispatch('payment-completed');

        } catch (\Exception $e) {
            session()->flash('error', 'Error al procesar el pago: ' . $e->getMessage());
        }
    }

    public function deletePayment($id)
    {
        Payment::findOrFail($id)->delete();
        session()->flash('message', 'La transacción fue eliminada correctamente del sistema.');
    }

    public function render()
    {
        $payments = Payment::query()
            ->with(['reservation.client'])
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('numero_transaccion', 'ilike', '%' . $this->search . '%')
                        ->orWhereHas('reservation.client', function ($q2) {
                            $q2->where('nombre', 'ilike', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->status, function ($q) {
                $q->where('estado', $this->status);
            })
            ->orderByDesc('created_at')
            ->paginate($this->perPage);

        // Obtenemos las reservas para el select (solo las que no han sido canceladas)
        $reservations = Reservation::with(['client', 'room'])
            ->where('estado', '!=', 'cancelada')
            ->orderByDesc('created_at')
            ->get();

        return view('livewire.payment-manager', [
            'payments' => $payments,
            'reservations' => $reservations
        ]);
    }
}