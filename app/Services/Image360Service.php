<?php

namespace App\Services;

use App\Models\Room;
use App\Models\Room360Image;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class Image360Service
{
    private const STORAGE_DISK = 'public';
    private const STORAGE_PATH = 'room-360-images';

    public function uploadImage(Room $room, UploadedFile $file, array $metadata): Room360Image
    {
        $filename = $this->generateFileName($file);
        $hash = hash_file('sha256', $file->getRealPath());

        $existingImage = Room360Image::where('hash', $hash)
            ->where('room_id', $room->id)
            ->first();

        if ($existingImage) {
            throw new \Exception('Esta imagen ya ha sido cargada para esta habitación.');
        }

        $path = $file->storeAs(
            self::STORAGE_PATH . '/room-' . $room->id,
            $filename,
            self::STORAGE_DISK
        );

        $fileSize = $file->getSize();
        $mimeType = $file->getMimeType();

        $image = Room360Image::create([
            'room_id' => $room->id,
            'filename' => $filename,
            'path' => $path,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
            'image_type' => $metadata['image_type'] ?? 'equirectangular',
            'horizontal_fov' => $metadata['horizontal_fov'] ?? 360,
            'vertical_fov' => $metadata['vertical_fov'] ?? 180,
            'yaw' => $metadata['yaw'] ?? 0,
            'pitch' => $metadata['pitch'] ?? 0,
            'roll' => $metadata['roll'] ?? 0,
            'metadata' => $metadata['metadata'] ?? [],
            'is_primary' => !$room->room360Images()->exists(),
            'hash' => $hash,
            'status' => 'active',
        ]);

        return $image;
    }

    public function deleteImage(Room360Image $image): bool
    {
        if ($image->is_primary && $image->room->room360Images()->count() > 1) {
            $nextImage = $image->room->room360Images()
                ->where('id', '!=', $image->id)
                ->first();
            $nextImage?->setPrimaryImage();
        }

        Storage::disk(self::STORAGE_DISK)->delete($image->path);

        return $image->delete();
    }

    public function reorderImages(Room $room, array $imageOrder): void
    {
        foreach ($imageOrder as $index => $imageId) {
            Room360Image::where('id', $imageId)
                ->where('room_id', $room->id)
                ->update(['display_order' => $index]);
        }
    }

    private function generateFileName(UploadedFile $file): string
    {
        $timestamp = now()->timestamp;
        $extension = $file->getClientOriginalExtension();
        return "image-{$timestamp}-" . uniqid() . ".{$extension}";
    }

    public function validateImage(UploadedFile $file): array
    {
        $errors = [];

        if ($file->getSize() > 52428800) {
            $errors[] = 'La imagen excede el tamaño máximo de 50MB.';
        }

        $mimeType = $file->getMimeType();
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];

        if (!in_array($mimeType, $allowedMimes)) {
            $errors[] = 'Formato de imagen no permitido. Use JPG, PNG o WebP.';
        }

        return $errors;
    }
}
