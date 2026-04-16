<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Client;
use Carbon\Carbon;

class ReportService
{
    public function getRevenueReport($dateFrom, $dateTo)
    {
        $payments = Payment::whereBetween('fecha_pago', [
            Carbon::createFromFormat('Y-m-d', $dateFrom)->startOfDay(),
            Carbon::createFromFormat('Y-m-d', $dateTo)->endOfDay()
        ])
        ->where('estado', 'completado')
        ->with('reservation')
        ->get();

        $totalRevenue = $payments->sum('monto');
        $totalReservations = $payments->count();

        return [
            'periodo_inicio' => $dateFrom,
            'periodo_fin' => $dateTo,
            'ingresos_totales' => $totalRevenue,
            'numero_pagos' => $totalReservations,
            'promedio_por_pago' => $totalReservations > 0 ? $totalRevenue / $totalReservations : 0,
            'pagos' => $payments->map(fn($p) => [
                'id' => $p->id,
                'monto' => $p->monto,
                'metodo' => $p->metodo_pago,
                'fecha' => $p->fecha_pago->format('Y-m-d H:i'),
                'numero_reserva' => $p->reservation_id,
            ])->toArray(),
        ];
    }

    public function getOccupancyReport($date)
    {
        $fecha = Carbon::createFromFormat('Y-m-d', $date)->startOfDay();

        $totalRooms = Room::where('is_active', true)->count();

        $occupiedRooms = Reservation::where('estado', '!=', 'cancelada')
            ->whereDate('fecha_inicio', '<=', $fecha)
            ->whereDate('fecha_fin', '>=', $fecha)
            ->count();

        $occupancyRate = $totalRooms > 0 ? ($occupiedRooms / $totalRooms) * 100 : 0;

        return [
            'fecha' => $date,
            'total_habitaciones' => $totalRooms,
            'ocupadas' => $occupiedRooms,
            'disponibles' => $totalRooms - $occupiedRooms,
            'porcentaje_ocupacion' => round($occupancyRate, 2),
        ];
    }

    public function getClientReport()
    {
        $clients = Client::where('is_active', true)
            ->withCount('reservations')
            ->get();

        return [
            'total_clientes' => $clients->count(),
            'total_reservas' => $clients->sum('reservations_count'),
            'clientes' => $clients->map(fn($c) => [
                'id' => $c->id,
                'nombre' => $c->nombre,
                'email' => $c->email,
                'tipo' => $c->tipo_cliente,
                'reservas' => $c->reservations_count,
            ])->toArray(),
        ];
    }

    public function getRoomReport()
    {
        $rooms = Room::where('is_active', true)
            ->with('rating')
            ->withCount('reservations')
            ->get();

        return [
            'total_habitaciones' => $rooms->count(),
            'habitaciones' => $rooms->map(fn($r) => [
                'id' => $r->id,
                'numero' => $r->numero,
                'tipo' => $r->tipo,
                'precio' => $r->precio_noche,
                'reservas' => $r->reservations_count,
                'rating' => $r->rating?->promedio ?? 0,
            ])->toArray(),
        ];
    }

    public function getPaymentMethodReport($dateFrom, $dateTo)
    {
        $payments = Payment::whereBetween('fecha_pago', [
            Carbon::createFromFormat('Y-m-d', $dateFrom)->startOfDay(),
            Carbon::createFromFormat('Y-m-d', $dateTo)->endOfDay()
        ])
        ->where('estado', 'completado')
        ->get()
        ->groupBy('metodo_pago');

        $resultado = [];
        foreach ($payments as $metodo => $pagosPorMetodo) {
            $resultado[] = [
                'metodo' => $metodo,
                'total_pagos' => $pagosPorMetodo->count(),
                'monto_total' => $pagosPorMetodo->sum('monto'),
                'promedio_pago' => $pagosPorMetodo->avg('monto'),
            ];
        }

        return $resultado;
    }
}
