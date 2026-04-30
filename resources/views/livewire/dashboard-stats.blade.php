<div class="space-y-8" wire:poll.10s="refreshStats">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Resumen de Hoy</h1>
            <p class="text-slate-500 mt-1.5 font-medium text-sm">
                @php
                    $days = ['Monday' => 'Lunes', 'Tuesday' => 'Martes', 'Wednesday' => 'Miércoles', 'Thursday' => 'Jueves', 'Friday' => 'Viernes', 'Saturday' => 'Sábado', 'Sunday' => 'Domingo'];
                    $months = ['January' => 'enero', 'February' => 'febrero', 'March' => 'marzo', 'April' => 'abril', 'May' => 'mayo', 'June' => 'junio', 'July' => 'julio', 'August' => 'agosto', 'September' => 'septiembre', 'October' => 'octubre', 'November' => 'noviembre', 'December' => 'diciembre'];
                    echo $days[now()->format('l')] . ", " . now()->format('d') . " de " . $months[now()->format('F')] . " de " . now()->format('Y');
                @endphp
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white/70 backdrop-blur-xl rounded-3xl border border-white shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-6 transition-all duration-300 hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-500 text-sm font-semibold tracking-wide">Reservas (Hoy)</p>
                    <p class="text-4xl font-extrabold text-slate-800 mt-2">{{ $reservations_today }}</p>
                </div>
                <div class="w-12 h-12 flex items-center justify-center bg-blue-500/10 rounded-2xl">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2m0 0V3a2 2 0 00-2-2h-2a2 2 0 00-2 2v2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white/70 backdrop-blur-xl rounded-3xl border border-white shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-6 transition-all duration-300 hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-500 text-sm font-semibold tracking-wide">Clientes (Hoy)</p>
                    <p class="text-4xl font-extrabold text-slate-800 mt-2">{{ $clients_today }}</p>
                </div>
                <div class="w-12 h-12 flex items-center justify-center bg-emerald-500/10 rounded-2xl">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.354a4 4 0 110 5.292m0 0H8.646m3.354 0H16m0 0a4 4 0 110-5.292m0 0H8.646m3.354 0H16"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white/70 backdrop-blur-xl rounded-3xl border border-white shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-6 transition-all duration-300 hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-500 text-sm font-semibold tracking-wide">Ingresos (Hoy)</p>
                    <p class="text-4xl font-extrabold text-slate-800 mt-2">${{ number_format($revenue_today, 2) }}</p>
                </div>
                <div class="w-12 h-12 flex items-center justify-center bg-amber-500/10 rounded-2xl">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white/70 backdrop-blur-xl rounded-3xl border border-white shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-6 transition-all duration-300 hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-500 text-sm font-semibold tracking-wide">Ocupación</p>
                    <p class="text-4xl font-extrabold text-slate-800 mt-2">{{ $occupancy_rate }}%</p>
                </div>
                <div class="w-12 h-12 flex items-center justify-center bg-purple-500/10 rounded-2xl">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white/80 backdrop-blur-xl rounded-3xl border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden">
            <div class="px-7 py-5 border-b border-slate-100/60 flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-800 tracking-tight">Reservas Recientes</h2>
                <a href="{{ route('admin.reservations') }}" class="text-blue-600 hover:text-blue-800 text-sm font-bold bg-blue-50 px-4 py-1.5 rounded-full transition-colors">Ver todas</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50/50">
                        <tr>
                            <th class="px-7 py-4 text-left font-bold text-slate-500 tracking-wide">Cliente</th>
                            <th class="px-7 py-4 text-left font-bold text-slate-500 tracking-wide">Habitación</th>
                            <th class="px-7 py-4 text-left font-bold text-slate-500 tracking-wide">Check-in</th>
                            <th class="px-7 py-4 text-left font-bold text-slate-500 tracking-wide">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100/60">
                        @forelse($recent_reservations as $reservation)
                            <tr class="hover:bg-slate-50/50 transition duration-200">
                                <td class="px-7 py-4 text-slate-800 font-semibold">{{ $reservation->client->name ?? 'N/A' }}</td>
                                <td class="px-7 py-4 text-slate-600">{{ $reservation->room->number ?? 'N/A' }}</td>
                                <td class="px-7 py-4 text-slate-600">{{ $reservation->check_in ? \Carbon\Carbon::parse($reservation->check_in)->format('d M, Y') : 'N/A' }}</td>
                                <td class="px-7 py-4">
                                    @php
                                        $statusStyles = match($reservation->status) {
                                            'confirmed', 'confirmada' => 'bg-emerald-100 text-emerald-700',
                                            'pending', 'pendiente' => 'bg-amber-100 text-amber-700',
                                            'cancelled', 'cancelada' => 'bg-red-100 text-red-700',
                                            default => 'bg-slate-100 text-slate-700'
                                        };
                                        $statusText = match($reservation->status) {
                                            'confirmed', 'confirmada' => 'Confirmada',
                                            'pending', 'pendiente' => 'Pendiente',
                                            'cancelled', 'cancelada' => 'Cancelada',
                                            default => ucfirst($reservation->status ?? 'N/A')
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $statusStyles }}">
                                        {{ $statusText }}
                                    </span>
                                </td>
                            </tr>
                        @forelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white/80 backdrop-blur-xl rounded-3xl border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-7 flex flex-col justify-center">
            <h2 class="text-lg font-bold text-slate-800 mb-6 tracking-tight text-center">Estado del Hotel</h2>
            
            <div class="bg-slate-50/80 rounded-2xl p-5 mb-6 border border-slate-100">
                <p class="text-sm font-bold text-slate-700 mb-3">Nivel de Ocupación</p>
                <div class="flex items-center justify-between mb-2">
                    <div class="w-full bg-slate-200/60 rounded-full h-3 mr-4 overflow-hidden">
                        <div class="bg-purple-500 h-3 rounded-full transition-all duration-1000 ease-out" style="width: {{ $occupancy_rate }}%"></div>
                    </div>
                    <span class="text-base font-extrabold text-slate-800">{{ $occupancy_rate }}%</span>
                </div>
                <p class="text-xs font-medium text-slate-500 text-right">{{ $stats['occupied_rooms'] ?? 0 }} de {{ $stats['total_rooms'] ?? 0 }} ocupadas</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="bg-blue-50/50 rounded-2xl p-4 border border-blue-100/50 text-center">
                    <p class="text-blue-600/80 text-xs font-bold mb-1 uppercase tracking-wider">Pendientes</p>
                    <p class="text-3xl font-extrabold text-blue-700">{{ $stats['pending_reservations'] ?? 0 }}</p>
                </div>
                <div class="bg-emerald-50/50 rounded-2xl p-4 border border-emerald-100/50 text-center">
                    <p class="text-emerald-600/80 text-xs font-bold mb-1 uppercase tracking-wider">Disponibles</p>
                    <p class="text-3xl font-extrabold text-emerald-700">{{ ($stats['total_rooms'] ?? 0) - ($stats['occupied_rooms'] ?? 0) }}</p>
                </div>
            </div>
        </div>
    </div>
</div>