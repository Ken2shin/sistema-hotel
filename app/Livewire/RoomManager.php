<?php

namespace App\Livewire;

use App\Models\Room;
use App\Models\RoomImage;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.app')]
class RoomManager extends Component
{
    use WithPagination;
    use WithFileUploads;

    public string $search = '';
    public int $perPage = 15;
    public bool $showForm = false;
    public string $formMode = 'create';
    public ?int $editingId = null;

    public string $numero = '';
    public string $tipo = '';
    public int $capacidad = 1;
    public float $precio_noche = 0;
    public float $precio_fin_semana = 0;
    public bool $is_active = true;
    public string $descripcion = '';
    public $imagen = null;
    
    // Variables para los servicios
    public array $servicios = [];
    public array $serviciosDisponibles = [
        'WiFi Gratis',
        'Desayuno Incluido',
        'Todo Incluido',
        'Aire Acondicionado',
        'Piscina',
        'Gimnasio',
        'Spa',
        'Restaurante',
        'Parqueo Gratis',
        'Mascotas Permitidas',
        'TV por Cable',
        'Caja Fuerte',
        'Vista al Mar'
    ];

    protected function rules()
    {
        return [
            'numero' => 'required|string|unique:rooms,numero' . ($this->editingId ? ",{$this->editingId}" : ''),
            'tipo' => 'required|in:single,double,triple,suite',
            'capacidad' => 'required|integer|min:1|max:10',
            'precio_noche' => 'required|numeric|min:0',
            'precio_fin_semana' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'descripcion' => 'nullable|string',
            'imagen' => 'nullable|image|max:5120',
            'servicios' => 'nullable|array',
        ];
    }

    public function render()
    {
        $rooms = Room::when($this->search, fn($q) => $q->where('numero', 'like', "%{$this->search}%")
                                                   ->orWhere('tipo', 'like', "%{$this->search}%"))
            ->orderBy('numero')
            ->paginate($this->perPage);

        return view('livewire.room-manager', compact('rooms'));
    }

    public function openCreateForm(): void
    {
        $this->resetForm();
        $this->formMode = 'create';
        $this->showForm = true;
    }

    public function openEditForm(int $id): void
    {
        $room = Room::find($id);
        if ($room) {
            $this->editingId = $id;
            $this->numero = $room->numero;
            $this->tipo = $room->tipo;
            $this->capacidad = $room->capacidad;
            $this->precio_noche = $room->precio_noche;
            $this->precio_fin_semana = $room->precio_fin_semana ?? 0;
            $this->is_active = $room->is_active;
            $this->descripcion = $room->descripcion ?? '';
            
            // Cargar los servicios existentes asegurando que sean un array
            $this->servicios = is_string($room->servicios) ? json_decode($room->servicios, true) : ($room->servicios ?? []);
            
            $this->formMode = 'edit';
            $this->showForm = true;
        }
    }

    public function save(): void
    {
        $this->validate();

        try {
            $data = [
                'numero' => $this->numero,
                'tipo' => $this->tipo,
                'capacidad' => $this->capacidad,
                'precio_noche' => $this->precio_noche,
                'precio_fin_semana' => $this->precio_fin_semana,
                'is_active' => $this->is_active,
                'descripcion' => $this->descripcion,
                'servicios' => json_encode($this->servicios), // Guardar los servicios como JSON
            ];

            if ($this->formMode === 'create') {
                $room = Room::create($data);
                session()->flash('success', 'Habitación creada exitosamente');
            } else {
                $room = Room::find($this->editingId);
                if ($room) {
                    $room->update($data);
                    session()->flash('success', 'Habitación actualizada exitosamente');
                }
            }

            if ($this->imagen && isset($room)) {
                $filename = time() . '_' . uniqid() . '.' . $this->imagen->getClientOriginalExtension();
                
                // Determinar el disco configurado (S3 Supabase o Local public)
                $disk = config('filesystems.default') === 'supabase' ? 'supabase' : 'public';
                
                $path = $this->imagen->storeAs('rooms', $filename, $disk);

                // Obtener URL según el disco
                if ($disk === 'supabase') {
                    $url = env('SUPABASE_S3_ENDPOINT') . '/' . env('SUPABASE_STORAGE_BUCKET') . '/rooms/' . $filename;
                } else {
                    $url = '/storage/' . $path;
                }

                RoomImage::where('room_id', $room->id)->update(['is_primary' => false]);

                RoomImage::create([
                    'room_id' => $room->id,
                    'path' => $url,
                    'filename' => $filename,
                    'is_primary' => true,
                ]);
            }

            $this->resetForm();
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function delete(int $id): void
    {
        $room = Room::find($id);
        if ($room) {
            $room->delete();
            session()->flash('success', 'Habitación eliminada');
        }
    }

    public function resetForm(): void
    {
        $this->reset(['numero', 'tipo', 'capacidad', 'precio_noche', 'precio_fin_semana', 'descripcion', 'editingId', 'imagen', 'servicios']);
        $this->is_active = true;
        $this->showForm = false;
        $this->resetValidation();
    }
}