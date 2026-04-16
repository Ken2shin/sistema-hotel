<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RoomImage;
use App\Models\Room360Image;
use App\Models\Room;
use App\Http\Requests\Store360ImageRequest;
use App\Services\Image360Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ImageController extends Controller
{
    public function __construct(private Image360Service $image360Service) {}

    public function uploadRoomImage(Request $request, int $roomId): JsonResponse
    {
        try {
            $room = Room::find($roomId);

            if (!$room) {
                return response()->json([
                    'success' => false,
                    'message' => 'Habitación no encontrada'
                ], 404);
            }

            $validated = $request->validate([
                'imagen' => 'required|image|mimes:jpeg,png,webp|max:5120',
                'is_primary' => 'sometimes|boolean',
            ]);

            if ($request->file('imagen')) {
                $file = $request->file('imagen');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                $manager = new ImageManager(new GdDriver());
                $image = $manager->read($file);

                $image->scale(width: 800);

                $path = 'hotel/rooms/' . $roomId . '/' . $filename;
                Storage::put($path, (string) $image->encode());

                $isPrimary = $validated['is_primary'] ?? false;

                if ($isPrimary) {
                    RoomImage::where('room_id', $roomId)->update(['is_primary' => false]);
                }

                $roomImage = RoomImage::create([
                    'room_id' => $roomId,
                    'path' => '/storage/' . $path,
                    'filename' => $filename,
                    'is_primary' => $isPrimary,
                ]);

                return response()->json([
                    'success' => true,
                    'data' => $roomImage,
                    'message' => 'Imagen subida exitosamente'
                ], 201);
            }

            return response()->json([
                'success' => false,
                'message' => 'No se proporcionó imagen'
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al subir la imagen'
            ], 500);
        }
    }

    public function getRoomImages(int $roomId): JsonResponse
    {
        try {
            $room = Room::find($roomId);

            if (!$room) {
                return response()->json([
                    'success' => false,
                    'message' => 'Habitación no encontrada'
                ], 404);
            }

            $images = RoomImage::where('room_id', $roomId)
                ->get()
                ->map(fn($img) => [
                    'id' => $img->id,
                    'path' => $img->path,
                    'filename' => $img->filename,
                    'is_primary' => $img->is_primary,
                ]);

            return response()->json([
                'success' => true,
                'data' => $images
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener imágenes'
            ], 500);
        }
    }

    public function deleteImage(int $imageId): JsonResponse
    {
        try {
            $image = RoomImage::find($imageId);

            if (!$image) {
                return response()->json([
                    'success' => false,
                    'message' => 'Imagen no encontrada'
                ], 404);
            }

            $path = str_replace('/storage/', '', $image->path);
            if (Storage::exists($path)) {
                Storage::delete($path);
            }

            $image->delete();

            return response()->json([
                'success' => true,
                'message' => 'Imagen eliminada exitosamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la imagen'
            ], 500);
        }
    }

    public function setPrimaryImage(int $imageId): JsonResponse
    {
        try {
            $image = RoomImage::find($imageId);

            if (!$image) {
                return response()->json([
                    'success' => false,
                    'message' => 'Imagen no encontrada'
                ], 404);
            }

            RoomImage::where('room_id', $image->room_id)->update(['is_primary' => false]);
            $image->update(['is_primary' => true]);

            return response()->json([
                'success' => true,
                'data' => $image,
                'message' => 'Imagen principal actualizada'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar imagen principal'
            ], 500);
        }
    }

    public function upload360Image(Store360ImageRequest $request): JsonResponse
    {
        try {
            $room = Room::findOrFail($request->input('room_id'));
            $file = $request->file('image');

            $validationErrors = $this->image360Service->validateImage($file);
            if (!empty($validationErrors)) {
                return response()->json(['errors' => $validationErrors], 422);
            }

            $metadata = [
                'image_type' => $request->input('image_type', 'equirectangular'),
                'horizontal_fov' => $request->input('horizontal_fov', 360),
                'vertical_fov' => $request->input('vertical_fov', 180),
                'yaw' => $request->input('yaw', 0),
                'pitch' => $request->input('pitch', 0),
                'roll' => $request->input('roll', 0),
                'metadata' => $request->input('metadata'),
            ];

            $image = $this->image360Service->uploadImage($room, $file, $metadata);

            Log::info('360 image uploaded', ['room_id' => $room->id, 'image_id' => $image->id]);

            return response()->json([
                'data' => $image->image_info,
                'message' => 'Imagen 360° subida exitosamente',
            ], 201);
        } catch (\Exception $e) {
            Log::error('360 image upload failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function get360Images(int $roomId): JsonResponse
    {
        try {
            $room = Room::findOrFail($roomId);
            $images = $room->room360Images()
                ->orderBy('display_order')
                ->get()
                ->map(fn($img) => $img->image_info);

            return response()->json(['data' => $images]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener imágenes 360°'], 500);
        }
    }

    public function delete360Image(Room360Image $image): JsonResponse
    {
        try {
            $this->image360Service->deleteImage($image);

            Log::info('360 image deleted', ['image_id' => $image->id]);

            return response()->json(['message' => 'Imagen 360° eliminada']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al eliminar'], 500);
        }
    }

    public function reorder360Images(Request $request, int $roomId): JsonResponse
    {
        try {
            $room = Room::findOrFail($roomId);
            $order = $request->input('order', []);

            if (empty($order)) {
                return response()->json(['error' => 'Orden no proporcionado'], 400);
            }

            $this->image360Service->reorderImages($room, $order);

            return response()->json(['message' => 'Orden actualizado']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al reordenar'], 500);
        }
    }

    public function setPrimary360Image(Room360Image $image): JsonResponse
    {
        try {
            $image->setPrimaryImage();

            return response()->json(['message' => 'Imagen primaria actualizada']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al actualizar'], 500);
        }
    }
}
