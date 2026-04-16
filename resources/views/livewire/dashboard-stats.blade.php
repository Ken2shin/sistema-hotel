<div class="space-y-8" wire:poll.5s="refreshStats">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Dashboard</h1>
            <p class="text-slate-600 mt-2">
                @php
                    $days = ['Monday' => 'Lunes', 'Tuesday' => 'Martes', 'Wednesday' => 'Miércoles', 'Thursday' => 'Jueves', 'Friday' => 'Viernes', 'Saturday' => 'Sábado', 'Sunday' => 'Domingo'];
                    $months = ['January' => 'enero', 'February' => 'febrero', 'March' => 'marzo', 'April' => 'abril', 'May' => 'mayo', 'June' => 'junio', 'July' => 'julio', 'August' => 'agosto', 'September' => 'septiembre', 'October' => 'octubre', 'November' => 'noviembre', 'December' => 'diciembre'];
                    $dayName = $days[now()->format('l')];
                    $monthName = $months[now()->format('F')];
                    echo "{$dayName}, " . now()->format('d') . " de {$monthName} de " . now()->format('Y');
                @endphp
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg border border-slate-200 p-6 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-600 text-sm font-medium">Reservas Totales</p>
                    <p class="text-3xl font-bold text-slate-900 mt-1">{{ $stats['total_reservations'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2m0 0V3a2 2 0 00-2-2h-2a2 2 0 00-2 2v2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-slate-200 p-6 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-600 text-sm font-medium">Clientes Registrados</p>
                    <p class="text-3xl font-bold text-slate-900 mt-1">{{ $stats['total_clients'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-gradient-to-br from-green-50 to-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292m0 0H8.646m3.354 0H16m0 0a4 4 0 110-5.292m0 0H8.646m3.354 0H16"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-slate-200 p-6 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-600 text-sm font-medium">Ingresos Totales</p>
                    <p class="text-3xl font-bold text-slate-900 mt-1">${{ number_format($stats['total_revenue'] ?? 0, 2) }}</p>
                </div>
                <div class="p-3 bg-gradient-to-br from-amber-50 to-amber-100 rounded-lg">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-slate-200 p-6 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-600 text-sm font-medium">Tasa de Ocupación</p>
                    <p class="text-3xl font-bold text-slate-900 mt-1">{{ $occupancy_rate ?? 0 }}%</p>
                </div>
                <div class="p-3 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-lg border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-900">Reservas Recientes</h2>
                <a href="{{ route('admin.reservations') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">Ver todas</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold text-slate-700">Cliente</th>
                            <th class="px-6 py-3 text-left font-semibold text-slate-700">Habitación</th>
                            <th class="px-6 py-3 text-left font-semibold text-slate-700">Check-in</th>
                            <th class="px-6 py-3 text-left font-semibold text-slate-700">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($recent_reservations as $reservation)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-3 text-slate-900">{{ $reservation->client->name ?? 'N/A' }}</td>
                                <td class="px-6 py-3 text-slate-900">{{ $reservation->room->number ?? 'N/A' }}</td>
                                <td class="px-6 py-3 text-slate-600">{{ $reservation->check_in?->format('d M, Y') ?? 'N/A' }}</td>
                                <td class="px-6 py-3">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                        @if($reservation->status === 'confirmed') bg-green-100 text-green-700
                                        @elseif($reservation->status === 'pending') bg-amber-100 text-amber-700
                                        @else bg-red-100 text-red-700
                                        @endif">
                                        {{ match($reservation->status) {
                                            'confirmed' => 'Confirmada',
                                            'pending' => 'Pendiente',
                                            'cancelled' => 'Cancelada',
                                            default => ucfirst($reservation->status)
                                        } }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-slate-600">
                                    <p class="text-sm">Sin reservas recientes</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-slate-200 p-6">
            <h2 class="text-lg font-bold text-slate-900 mb-4">Ocupación e Ingresos</h2>
            <div class="flex flex-col items-center justify-center py-8">
                <div class="text-4xl font-bold text-slate-900">${{ number_format($revenue_today ?? 0, 2) }}</div>
                <p class="text-slate-600 text-sm mt-1">Ingresos de hoy</p>
                <div class="mt-6 w-full space-y-4">
                    <div class="bg-slate-50 rounded-lg p-4">
                        <p class="text-sm font-medium text-slate-900 mb-2">Ocupación Actual</p>
                        <div class="flex items-center justify-between">
                            <div class="w-full bg-slate-200 rounded-full h-2 mr-3">
                                <div class="bg-purple-600 h-2 rounded-full transition-all" style="width: {{ $occupancy_rate }}%"></div>
                            </div>
                            <span class="text-sm font-bold text-slate-900">{{ $occupancy_rate }}%</span>
                        </div>
                        <p class="text-xs text-slate-600 mt-2">{{ $stats['occupied_rooms'] ?? 0 }} de {{ $stats['total_rooms'] ?? 0 }} habitaciones</p>
                    </div>
                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div class="bg-blue-50 rounded-lg p-3">
                            <p class="text-blue-600 font-medium">Reservas Pendientes</p>
                            <p class="text-2xl font-bold text-blue-700">{{ $stats['pending_reservations'] ?? 0 }}</p>
                        </div>
                        <div class="bg-green-50 rounded-lg p-3">
                            <p class="text-green-600 font-medium">Disponibles</p>
                            <p class="text-2xl font-bold text-green-700">{{ ($stats['total_rooms'] ?? 0) - ($stats['occupied_rooms'] ?? 0) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
