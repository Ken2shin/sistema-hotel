<?php

namespace App\Livewire;

use App\Models\Room;
use App\Models\RoomImage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str; // <-- Añadido para limpiar strings

#[Layout('layouts.app')]
class ImageManager extends Component
{
    use WithFileUploads;

    public $roomId = null;
    public $room = null;
    public $rooms = [];
    public $images = [];
    public $imagen = null;

    public function mount($roomId = null)
    {
        $this->rooms = Room::orderBy('numero')->get();
        
        if ($roomId) {
            $this->selectRoom($roomId);
        }
    }

    public function updatedRoomId($value)
    {
        if ($value) {
            $this->selectRoom($value);
        } else {
            $this->resetRoom();
        }
    }

    public function selectRoom($id)
    {
        $this->roomId = $id;
        $this->room = Room::find($id);
        $this->loadImages();
    }

    public function resetRoom()
    {
        $this->roomId = null;
        $this->room = null;
        $this->images = [];
    }

    public function loadImages()
    {
        if ($this->roomId) {
            $this->images = RoomImage::where('room_id', $this->roomId)
                ->orderBy('is_primary', 'desc')
                ->orderBy('created_at', 'desc')
                ->get()
                ->toArray();
        }
    }

    public function render()
    {
        return view('livewire.image-manager');
    }

    public function uploadImage()
    {
        $this->validate([
            'imagen' => 'required|image|max:5120',
            'roomId' => 'required',
        ]);

        try {
            $file = $this->imagen;
            
            // 1. Limpiamos el roomId para asegurar que no haya espacios
            $cleanRoomId = trim(strval($this->roomId));
            
            // 2. Generamos el nombre sin usar espacios ni caracteres especiales
            $filename = 'room_' . $cleanRoomId . '_' . time() . '_' . Str::random(5) . '.' . $file->getClientOriginalExtension();
            
            $disk = config('filesystems.default') === 'supabase' ? 'supabase' : 'public';

            if ($disk === 'supabase') {
                // Guardar en la RAIZ del bucket
                $file->storeAs('', $filename, 'supabase');
                
                // Construir URL limpia garantizando que no haya espacios escondidos
                $baseUrl = rtrim(env('SUPABASE_URL'), '/');
                $bucketName = trim(env('SUPABASE_STORAGE_BUCKET'));
                $imageUrl = "{$baseUrl}/storage/v1/object/public/{$bucketName}/{$filename}";
                
            } else {
                $path = $file->storeAs('room', $filename, 'public');
                $imageUrl = Storage::disk('public')->url($path);
            }
            
            $isPrimary = RoomImage::where('room_id', $this->roomId)->count() === 0;
            
            RoomImage::create([
                'room_id' => $this->roomId,
                'path' => $imageUrl,
                'filename' => $filename,
                'is_primary' => $isPrimary,
            ]);

            $this->imagen = null;
            $this->loadImages();
            
            session()->flash('success', 'Imagen subida y guardada exitosamente');
            $this->dispatch('image-uploaded');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al subir la imagen: ' . $e->getMessage());
        }
    }

    public function deleteImage($imageId)
    {
        try {
            $image = RoomImage::find($imageId);
            
            if ($image) {
                $wasPrimary = $image->is_primary;
                $image->delete();
                
                if ($wasPrimary && $this->roomId) {
                    $nextImage = RoomImage::where('room_id', $this->roomId)->first();
                    if ($nextImage) {
                        $nextImage->update(['is_primary' => true]);
                    }
                }
                
                $this->loadImages();
                session()->flash('success', 'Imagen eliminada exitosamente');
                $this->dispatch('image-deleted');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }

    public function setPrimaryImage($imageId)
    {
        try {
            $image = RoomImage::find($imageId);
            
            if ($image && $this->roomId) {
                RoomImage::where('room_id', $this->roomId)->update(['is_primary' => false]);
                $image->update(['is_primary' => true]);
                
                $this->loadImages();
                session()->flash('success', 'Imagen principal actualizada');
                $this->dispatch('image-updated');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar imagen: ' . $e->getMessage());
        }
    }
}