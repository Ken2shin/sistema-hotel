<?php

namespace App\Services;

use App\Models\Report;
use App\Models\Reservation;
use App\Models\Payment;
use App\Models\Client;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ReportGeneratorService
{
    public function generateRevenueReport(Report $report): array
    {
        $startDate = Carbon::parse($report->start_date)->startOfDay();
        $endDate = Carbon::parse($report->end_date)->endOfDay();

        // Ajustado a las columnas de tu BD: fecha_pago, estado, monto, metodo_pago
        $payments = Payment::whereBetween('fecha_pago', [$startDate, $endDate])
            ->whereIn('estado', ['completado', 'pagado', 'confirmado']) // Ajusta según los estados que uses
            ->get();

        $totalRevenue = $payments->sum('monto');
        $totalTransactions = $payments->count();

        $dailyRevenue = $this->groupByDate($payments, 'fecha_pago', 'monto');

        $paymentMethods = $payments->groupBy('metodo_pago')
            ->map(fn($group) => [
                'cantidad' => $group->count(),
                'total_recaudado' => round($group->sum('monto'), 2),
                'porcentaje_del_total' => $totalRevenue > 0 ? round(($group->sum('monto') / $totalRevenue) * 100, 2) . '%' : '0%',
            ]);

        // Retornamos el array exacto que el Modal y el PDF esperan leer
        return [
            'resumen_financiero' => [
                'periodo_evaluado' => $startDate->format('d/m/Y') . ' al ' . $endDate->format('d/m/Y'),
                'ingresos_totales' => $totalRevenue,
                'cantidad_transacciones' => $totalTransactions,
                'promedio_por_transaccion' => $totalTransactions > 0 ? round($totalRevenue / $totalTransactions, 2) : 0,
                'metodo_mas_usado' => $paymentMethods->sortByDesc('total_recaudado')->keys()->first() ?? 'N/A',
            ],
            'desglose_diario' => $dailyRevenue,
            'metodos_de_pago' => $paymentMethods->toArray(),
        ];
    }

    public function generateOccupancyReport(Report $report): array
    {
        $startDate = Carbon::parse($report->start_date)->startOfDay();
        $endDate = Carbon::parse($report->end_date)->endOfDay();

        // Evitar división por cero si seleccionan el mismo día
        $totalDays = max(1, $endDate->diffInDays($startDate)); 
        $totalRooms = Room::where('is_active', true)->count();
        $totalCapacity = $totalRooms * $totalDays;

        // Ajustado a tus columnas: estado, fecha_inicio, fecha_fin
        $reservations = Reservation::where('estado', '!=', 'cancelada')
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('fecha_inicio', [$startDate, $endDate])
                    ->orWhereBetween('fecha_fin', [$startDate, $endDate]);
            })
            ->get();

        $occupiedDays = 0;
        foreach ($reservations as $reservation) {
            $start = max(Carbon::parse($reservation->fecha_inicio), $startDate);
            $end = min(Carbon::parse($reservation->fecha_fin), $endDate);
            
            if ($end->greaterThan($start)) {
                $occupiedDays += $end->diffInDays($start);
            }
        }

        $occupancyRate = $totalCapacity > 0 ? round(($occupiedDays / $totalCapacity) * 100, 2) : 0;

        return [
            'resumen_de_ocupacion' => [
                'periodo_evaluado' => $startDate->format('d/m/Y') . ' al ' . $endDate->format('d/m/Y'),
                'porcentaje_ocupacion_global' => $occupancyRate . '%',
                'total_reservaciones_en_periodo' => $reservations->count(),
            ],
            'metricas_detalladas' => [
                'habitaciones_disponibles' => $totalRooms,
                'capacidad_maxima_dias' => $totalCapacity,
                'dias_efectivos_ocupados' => $occupiedDays,
                'promedio_dias_por_habitacion' => $totalRooms > 0 ? round($occupiedDays / $totalRooms, 2) : 0,
            ]
        ];
    }

    public function generateClientReport(Report $report): array
    {
        $startDate = Carbon::parse($report->start_date)->startOfDay();
        $endDate = Carbon::parse($report->end_date)->endOfDay();

        // Extrae clientes que tengan reservaciones creadas en ese rango de fechas
        $clients = Client::whereHas('reservations', function ($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate]);
        })->with(['reservations' => function ($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate]);
        }])->get();

        $totalClients = $clients->count();
        $totalReservations = $clients->sum(fn($c) => $c->reservations->count());
        $repeatClients = $clients->filter(fn($c) => $c->reservations->count() > 1)->count();
        $newClients = $totalClients - $repeatClients;

        return [
            'resumen_de_clientes' => [
                'periodo_evaluado' => $startDate->format('d/m/Y') . ' al ' . $endDate->format('d/m/Y'),
                'clientes_atendidos_periodo' => $totalClients,
                'clientes_nuevos' => $newClients,
                'clientes_recurrentes' => $repeatClients,
            ],
            'metricas_de_fidelizacion' => [
                'reservaciones_totales_vinculadas' => $totalReservations,
                'promedio_reservas_por_cliente' => $totalClients > 0 ? round($totalReservations / $totalClients, 2) : 0,
                'tasa_de_retencion' => $totalClients > 0 ? round(($repeatClients / $totalClients) * 100, 2) . '%' : '0%',
            ]
        ];
    }

    /**
     * Función helper genérica para agrupar ingresos diarios
     */
    private function groupByDate($collection, $dateField, $amountField)
    {
        return $collection->groupBy(function ($item) use ($dateField) {
            return Carbon::parse($item->{$dateField})->format('Y-m-d');
        })->map(fn($group) => round($group->sum($amountField), 2))->toArray();
    }
}