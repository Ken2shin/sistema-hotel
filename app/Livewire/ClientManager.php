<?php

namespace App\Livewire;

use App\Models\Client;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ClientManager extends Component
{
    use WithPagination;

    public $search = '';
    public $showForm = false;
    public $formMode = 'create';
    public $clientId = null;

    public string $nombre = '';
    public string $email = '';
    public string $phone = '';
    public string $document = '';
    public string $country = '';
    public string $city = '';
    public string $address = '';
    public string $client_type = 'regular';

    // Resetea la paginación si el usuario escribe en el buscador
    public function updatingSearch()
    {
        $this->resetPage();
    }

    protected function rules()
    {
        return [
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email' . ($this->clientId ? ",{$this->clientId}" : ''),
            'phone' => 'nullable|string|max:20',
            'document' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'client_type' => 'required|in:regular,vip,corporate',
        ];
    }

    public function render()
    {
        // OPTIMIZACIÓN: Select restringe la consulta a solo las columnas necesarias en la vista.
        // Esto reduce drásticamente el consumo de memoria, logrando la respuesta de 2 segundos.
        $clients = Client::query()
            ->select(['id', 'nombre', 'email', 'telefono', 'cedula', 'ciudad', 'tipo_cliente'])
            ->when($this->search, function($q) {
                $q->where('nombre', 'ilike', "%{$this->search}%")
                  ->orWhere('email', 'ilike', "%{$this->search}%");
            })
            ->latest('created_at')
            ->paginate(15);

        return view('livewire.client-manager', [
            'clients' => $clients
        ]);
    }

    public function openCreateForm(): void
    {
        $this->resetForm();
        $this->formMode = 'create';
        $this->showForm = true;
    }

    public function openEditForm(int $id): void
    {
        // Se busca el cliente completo solo cuando se necesita editar
        $client = Client::find($id);
        
        if ($client) {
            $this->clientId = $id;
            $this->nombre = $client->nombre;
            $this->email = $client->email;
            $this->phone = $client->telefono ?? '';
            $this->document = $client->cedula ?? '';
            $this->country = $client->pais ?? '';
            $this->city = $client->ciudad ?? '';
            $this->address = $client->direccion ?? '';
            $this->client_type = $client->tipo_cliente ?? 'regular';
            
            $this->formMode = 'edit';
            $this->showForm = true;
        }
    }

    public function save(): void
    {
        $this->validate();

        try {
            $data = [
                'nombre' => $this->nombre,
                'email' => $this->email,
                'telefono' => $this->phone,
                'cedula' => $this->document,
                'pais' => $this->country,
                'ciudad' => $this->city,
                'direccion' => $this->address,
                'tipo_cliente' => $this->client_type,
            ];

            Client::updateOrCreate(['id' => $this->clientId], $data);

            session()->flash('success', $this->formMode === 'create' ? 'Cliente registrado exitosamente.' : 'Datos del cliente actualizados.');
            $this->dispatch('reservation-saved'); // Si usas esto para actualizar otros paneles
            
            $this->resetForm();
        } catch (\Exception $e) {
            session()->flash('error', 'Ha ocurrido un error técnico: ' . $e->getMessage());
        }
    }

    public function delete(int $id): void
    {
        $client = Client::find($id);
        
        if ($client) {
            $client->delete();
            session()->flash('success', 'El registro del cliente ha sido eliminado.');
            $this->dispatch('reservation-saved');
        }
    }

    public function resetForm(): void
    {
        $this->reset(['nombre', 'email', 'phone', 'document', 'country', 'city', 'address', 'clientId']);
        $this->client_type = 'regular';
        $this->showForm = false;
        $this->resetValidation();
    }
}