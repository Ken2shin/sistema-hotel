<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoomVisualizationController extends Controller
{
    public function getHotelFloorPlan(Request $request): JsonResponse
    {
        try {
            $fecha = $request->query('fecha') ?? now()->format('Y-m-d');

            $rooms = Room::where('is_active', true)
                ->with('reservations')
                ->get()
                ->map(function ($room) use ($fecha) {
                    $fechaBusqueda = Carbon::createFromFormat('Y-m-d', $fecha)->startOfDay();

                    $reservacionesDelDia = $room->reservations()
                        ->where('estado', '!=', 'cancelada')
                        ->where(function ($query) use ($fechaBusqueda) {
                            $query->whereDate('fecha_inicio', '<=', $fechaBusqueda)
                                  ->whereDate('fecha_fin', '>=', $fechaBusqueda);
                        })
                        ->get();

                    $estado = 'disponible';
                    if ($reservacionesDelDia->isNotEmpty()) {
                        $estado = 'ocupada';
                    }

                    $numeroFloor = intval(substr($room->numero, 0, 1));
                    $numeroHabitacion = intval(substr($room->numero, 1));

                    return [
                        'id' => $room->id,
                        'numero' => $room->numero,
                        'tipo' => $room->tipo,
                        'capacidad' => $room->capacidad,
                        'precio_noche' => (float) $room->precio_noche,
                        'estado' => $estado,
                        'piso' => $numeroFloor,
                        'posicion_x' => ($numeroHabitacion - 1) * 2,
                        'posicion_y' => $numeroFloor * 3,
                        'posicion_z' => 0,
                        'ancho' => 2,
                        'alto' => 2.5,
                        'profundo' => 3,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'fecha' => $fecha,
                    'habitaciones' => $rooms,
                    'estadisticas' => [
                        'total' => $rooms->count(),
                        'disponibles' => $rooms->where('estado', 'disponible')->count(),
                        'ocupadas' => $rooms->where('estado', 'ocupada')->count(),
                        'reservadas' => $rooms->where('estado', 'reservada')->count(),
                    ]
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener plano del hotel'
            ], 500);
        }
    }

    public function getRoomState(int $roomId, Request $request): JsonResponse
    {
        try {
            $fecha = $request->query('fecha') ?? now()->format('Y-m-d');
            $room = Room::with('reservations')->find($roomId);

            if (!$room) {
                return response()->json([
                    'success' => false,
                    'message' => 'Habitación no encontrada'
                ], 404);
            }

            $fechaBusqueda = Carbon::createFromFormat('Y-m-d', $fecha)->startOfDay();

            $reservacion = $room->reservations()
                ->where('estado', '!=', 'cancelada')
                ->where(function ($query) use ($fechaBusqueda) {
                    $query->whereDate('fecha_inicio', '<=', $fechaBusqueda)
                          ->whereDate('fecha_fin', '>=', $fechaBusqueda);
                })
                ->first();

            $estado = 'disponible';
            $detallesReservacion = null;

            if ($reservacion) {
                $estado = 'ocupada';
                $detallesReservacion = [
                    'id' => $reservacion->id,
                    'cliente' => $reservacion->client->nombre,
                    'fecha_inicio' => $reservacion->fecha_inicio->format('Y-m-d H:i'),
                    'fecha_fin' => $reservacion->fecha_fin->format('Y-m-d H:i'),
                    'estado' => $reservacion->estado,
                ];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'habitacion' => [
                        'id' => $room->id,
                        'numero' => $room->numero,
                        'tipo' => $room->tipo,
                        'capacidad' => $room->capacidad,
                        'precio_noche' => (float) $room->precio_noche,
                        'estado' => $estado,
                        'reservacion' => $detallesReservacion,
                    ]
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estado de habitación'
            ], 500);
        }
    }
}
