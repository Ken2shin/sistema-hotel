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
        $clients = Client::when($this->search, fn($q) => $q->where('nombre', 'like', "%{$this->search}%")
                                                         ->orWhere('email', 'like', "%{$this->search}%"))
            ->orderBy('created_at', 'desc')
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
        $client = Client::find($id);
        if ($client) {
            $this->clientId = $id;
            $this->nombre = $client->nombre;
            $this->email = $client->email;
            $this->phone = $client->phone;
            $this->document = $client->document;
            $this->country = $client->country;
            $this->city = $client->city;
            $this->address = $client->address;
            $this->client_type = $client->client_type ?? 'regular';
            $this->formMode = 'edit';
            $this->showForm = true;
        }
    }

    public function save(): void
    {
        $this->validate();

        try {
            if ($this->formMode === 'create') {
                Client::create([
                    'nombre' => $this->nombre,
                    'email' => $this->email,
                    'phone' => $this->phone,
                    'document' => $this->document,
                    'country' => $this->country,
                    'city' => $this->city,
                    'address' => $this->address,
                    'client_type' => $this->client_type,
                ]);
                session()->flash('success', 'Cliente creado exitosamente');
                $this->dispatch('reservation-saved');
            } else {
                $client = Client::find($this->clientId);
                if ($client) {
                    $client->update([
                        'nombre' => $this->nombre,
                        'email' => $this->email,
                        'phone' => $this->phone,
                        'document' => $this->document,
                        'country' => $this->country,
                        'city' => $this->city,
                        'address' => $this->address,
                        'client_type' => $this->client_type,
                    ]);
                    session()->flash('success', 'Cliente actualizado exitosamente');
                    $this->dispatch('reservation-saved');
                }
            }
            $this->resetForm();
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function delete(int $id): void
    {
        $client = Client::find($id);
        if ($client) {
            $client->delete();
            session()->flash('success', 'Cliente eliminado');
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