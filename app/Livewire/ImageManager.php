<?php

namespace App\Livewire;

use App\Models\Room;
use App\Models\RoomImage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
        $value ? $this->selectRoom($value) : $this->resetRoom();
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
            $cleanRoomId = trim(strval($this->roomId));
            
            // Genera un nombre seguro y único
            $filename = sprintf('room_%s_%s_%s.%s', $cleanRoomId, time(), Str::random(5), $file->getClientOriginalExtension());
            
            // Obtenemos el disco por defecto (será 'supabase' gracias a tu .env)
            $disk = config('filesystems.default');

            // Guarda el archivo en la RAÍZ del bucket usando '/'
            $file->storeAs('/', $filename, $disk);
            
            // Genera la URL pública usando el nombre del archivo directamente
            $imageUrl = Storage::disk($disk)->url($filename);
            
            $isPrimary = RoomImage::where('room_id', $this->roomId)->doesntExist();
            
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
                $disk = config('filesystems.default');
                
                // Eliminamos el archivo buscando directamente su nombre en la raíz del bucket
                if (Storage::disk($disk)->exists($image->filename)) {
                    Storage::disk($disk)->delete($image->filename);
                }

                $wasPrimary = $image->is_primary;
                $image->delete();
                
                if ($wasPrimary && $this->roomId) {
                    $nextImage = RoomImage::where('room_id', $this->roomId)->first();
                    $nextImage?->update(['is_primary' => true]);
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