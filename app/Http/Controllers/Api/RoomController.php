<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Review;
use App\Models\RoomRating;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RoomController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $rooms = Room::with(['images', 'rating', 'room360Images'])
                ->where('is_active', true)
                ->paginate(20);

            return response()->json([
                'data' => $rooms->items(),
                'pagination' => [
                    'current_page' => $rooms->currentPage(),
                    'total' => $rooms->total(),
                    'per_page' => $rooms->perPage(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Room index failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Error fetching rooms'], 500);
        }
    }

    public function show(Room $room): JsonResponse
    {
        try {
            $room->load(['images', 'rating', 'room360Images', 'reservations']);

            return response()->json([
                'data' => [
                    'id' => $room->id,
                    'numero' => $room->numero,
                    'tipo' => $room->tipo,
                    'capacidad' => $room->capacidad,
                    'precio_noche' => (float) $room->precio_noche,
                    'precio_fin_semana' => (float) $room->precio_fin_semana,
                    'descripcion' => $room->descripcion,
                    'servicios' => $room->servicios,
                    'is_active' => $room->is_active,
                    'imagenes' => $room->images->map(fn($img) => [
                        'id' => $img->id,
                        'path' => $img->path,
                        'is_primary' => $img->is_primary,
                    ]),
                    'imagenes_360' => $room->room360Images->map(fn($img) => $img->image_info),
                    'rating' => $room->rating?->promedio ?? 0,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Room not found'], 404);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'numero' => 'required|string|unique:rooms',
                'tipo' => 'required|string',
                'capacidad' => 'required|integer|min:1',
                'precio_noche' => 'required|numeric|min:0',
                'precio_fin_semana' => 'required|numeric|min:0',
                'descripcion' => 'nullable|string',
                'servicios' => 'nullable|json',
            ]);

            $room = Room::create($validated);

            Log::info('Room created', ['room_id' => $room->id, 'user_id' => auth()->id()]);

            return response()->json(['data' => $room], 201);
        } catch (\Exception $e) {
            Log::error('Room creation failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function update(Request $request, Room $room): JsonResponse
    {
        try {
            $validated = $request->validate([
                'numero' => "required|string|unique:rooms,numero,{$room->id}",
                'tipo' => 'required|string',
                'capacidad' => 'required|integer|min:1',
                'precio_noche' => 'required|numeric|min:0',
                'precio_fin_semana' => 'required|numeric|min:0',
                'descripcion' => 'nullable|string',
                'servicios' => 'nullable|json',
                'is_active' => 'boolean',
            ]);

            $room->update($validated);

            Log::info('Room updated', ['room_id' => $room->id]);

            return response()->json(['data' => $room]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function destroy(Room $room): JsonResponse
    {
        try {
            $room->delete();

            Log::info('Room deleted', ['room_id' => $room->id]);

            return response()->json(['message' => 'Room deleted']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error deleting room'], 500);
        }
    }

    public function available(Request $request): JsonResponse
    {
        try {
            $inicio = $request->query('inicio');
            $fin = $request->query('fin');
            $min = $request->query('min');
            $max = $request->query('max');
            $servicios = $request->query('servicios');

            if (!$inicio || !$fin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Las fechas inicio y fin son requeridas'
                ], 400);
            }

            $fechaInicio = Carbon::createFromFormat('Y-m-d', $inicio)->startOfDay();
            $fechaFin = Carbon::createFromFormat('Y-m-d', $fin)->endOfDay();

            $query = Room::with(['images', 'rating'])
                ->where('is_active', true);

            if ($min) {
                $query->where('precio_noche', '>=', (float) $min);
            }

            if ($max) {
                $query->where('precio_noche', '<=', (float) $max);
            }

            $disponibles = $query->get()
                ->filter(function ($room) use ($fechaInicio, $fechaFin) {
                    return $room->isAvailable($fechaInicio, $fechaFin);
                })
                ->values();

            if ($servicios) {
                $serviciosRequeridos = explode(',', $servicios);
                $disponibles = $disponibles->filter(function ($room) use ($serviciosRequeridos) {
                    $roomServicios = $room->servicios ?? [];
                    foreach ($serviciosRequeridos as $servicio) {
                        if (!in_array(trim($servicio), $roomServicios)) {
                            return false;
                        }
                    }
                    return true;
                })->values();
            }

            $resultado = $disponibles->map(function ($room) {
                return [
                    'id' => $room->id,
                    'numero' => $room->numero,
                    'tipo' => $room->tipo,
                    'capacidad' => $room->capacidad,
                    'precio_noche' => (float) $room->precio_noche,
                    'precio_fin_semana' => (float) $room->precio_fin_semana,
                    'descripcion' => $room->descripcion,
                    'servicios' => $room->servicios,
                    'imagen_principal' => $room->primaryImage()?->path,
                    'rating' => $room->rating?->promedio,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $resultado
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar habitaciones disponibles'
            ], 500);
        }
    }

    public function reviews(int $id): JsonResponse
    {
        try {
            $room = Room::find($id);

            if (!$room) {
                return response()->json([
                    'success' => false,
                    'message' => 'Habitación no encontrada'
                ], 404);
            }

            $reviews = Review::where('room_id', $id)
                ->where('is_verified', true)
                ->with(['client'])
                ->get()
                ->map(fn($review) => [
                    'id' => $review->id,
                    'calificacion' => $review->calificacion,
                    'comentario' => $review->comentario,
                    'cliente' => $review->client->nombre,
                    'fecha' => $review->created_at->format('Y-m-d'),
                ]);

            return response()->json([
                'success' => true,
                'data' => $reviews
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener reseñas'
            ], 500);
        }
    }

    public function rating(int $id): JsonResponse
    {
        try {
            $room = Room::find($id);

            if (!$room) {
                return response()->json([
                    'success' => false,
                    'message' => 'Habitación no encontrada'
                ], 404);
            }

            $rating = RoomRating::where('room_id', $id)->first();

            if (!$rating) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'promedio' => 0,
                        'total_resenas' => 0,
                    ]
                ], 200);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'promedio' => (float) $rating->promedio,
                    'total_resenas' => $rating->total_resenas,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener calificación'
            ], 500);
        }
    }

    public function recommended(Request $request): JsonResponse
    {
        try {
            $presupuesto = $request->query('presupuesto');
            $inicio = $request->query('inicio');
            $fin = $request->query('fin');
            $servicios = $request->query('servicios');

            $query = Room::with(['images', 'rating'])
                ->where('is_active', true);

            if ($presupuesto) {
                $query->where('precio_noche', '<=', (float) $presupuesto);
            }

            $recomendadas = $query->get();

            if ($inicio && $fin) {
                $fechaInicio = Carbon::createFromFormat('Y-m-d', $inicio)->startOfDay();
                $fechaFin = Carbon::createFromFormat('Y-m-d', $fin)->endOfDay();

                $recomendadas = $recomendadas->filter(function ($room) use ($fechaInicio, $fechaFin) {
                    return $room->isAvailable($fechaInicio, $fechaFin);
                });
            }

            if ($servicios) {
                $serviciosRequeridos = explode(',', $servicios);
                $recomendadas = $recomendadas->filter(function ($room) use ($serviciosRequeridos) {
                    $roomServicios = $room->servicios ?? [];
                    foreach ($serviciosRequeridos as $servicio) {
                        if (!in_array(trim($servicio), $roomServicios)) {
                            return false;
                        }
                    }
                    return true;
                });
            }

            $resultado = $recomendadas->sortByDesc('rating.promedio')
                ->take(5)
                ->values()
                ->map(function ($room) {
                    return [
                        'id' => $room->id,
                        'numero' => $room->numero,
                        'tipo' => $room->tipo,
                        'capacidad' => $room->capacidad,
                        'precio_noche' => (float) $room->precio_noche,
                        'precio_fin_semana' => (float) $room->precio_fin_semana,
                        'descripcion' => $room->descripcion,
                        'servicios' => $room->servicios,
                        'imagen_principal' => $room->primaryImage()?->path,
                        'rating' => $room->rating?->promedio ?? 0,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $resultado
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener habitaciones recomendadas'
            ], 500);
        }
    }
}
