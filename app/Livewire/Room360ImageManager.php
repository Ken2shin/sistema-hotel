<?php

namespace App\Livewire;

use App\Models\Room;
use App\Models\Room360Image;
use App\Services\Image360Service;
use Livewire\Component;
use Livewire\WithFileUploads;

class Room360ImageManager extends Component
{
    use WithFileUploads;

    public Room $room;
    public $image = null;
    public $imageType = 'equirectangular';
    public $horizontalFov = 360;
    public $verticalFov = 180;
    public $yaw = 0;
    public $pitch = 0;
    public $roll = 0;
    public $isUploading = false;
    public $uploadProgress = 0;
    public $showUploadForm = false;

    protected Image360Service $image360Service;

    public function mount(Room $room)
    {
        $this->room = $room;
        $this->image360Service = app(Image360Service::class);
    }

    public function render()
    {
        return view('livewire.room-360-image-manager', [
            'images' => $this->room->room360Images()->orderBy('display_order')->get(),
        ]);
    }

    public function uploadImage()
    {
        $this->validate([
            'image' => 'required|file|mimes:jpg,jpeg,png,webp|max:52428800',
            'imageType' => 'required|in:equirectangular,cubemap,spherical,standard',
        ]);

        $this->isUploading = true;

        try {
            $metadata = [
                'image_type' => $this->imageType,
                'horizontal_fov' => $this->horizontalFov,
                'vertical_fov' => $this->verticalFov,
                'yaw' => $this->yaw,
                'pitch' => $this->pitch,
                'roll' => $this->roll,
            ];

            $this->image360Service->uploadImage($this->room, $this->image, $metadata);

            session()->flash('message', 'Imagen 360° subida exitosamente');
            $this->resetUploadForm();
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        } finally {
            $this->isUploading = false;
        }
    }

    public function deleteImage(Room360Image $image)
    {
        try {
            $this->image360Service->deleteImage($image);
            session()->flash('message', 'Imagen eliminada');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar');
        }
    }

    public function setPrimaryImage(Room360Image $image)
    {
        try {
            $image->setPrimaryImage();
            session()->flash('message', 'Imagen principal actualizada');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar');
        }
    }

    public function reorderImages($order)
    {
        try {
            $this->image360Service->reorderImages($this->room, $order);
            session()->flash('message', 'Orden actualizado');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al reordenar');
        }
    }

    private function resetUploadForm()
    {
        $this->image = null;
        $this->imageType = 'equirectangular';
        $this->horizontalFov = 360;
        $this->verticalFov = 180;
        $this->yaw = 0;
        $this->pitch = 0;
        $this->roll = 0;
        $this->showUploadForm = false;
    }
}
