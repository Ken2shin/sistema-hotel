<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Gestión de Reservas</h1>
            <p class="text-slate-600 mt-1">Administra todas las reservas del hotel</p>
        </div>
        <button wire:click="openCreateForm" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
            Nueva Reserva
        </button>
    </div>

    @if(session()->has('message'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 flex items-center justify-between">
            <p class="text-green-800 font-medium">{{ session('message') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif

    @if(session()->has('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 flex items-center justify-between">
            <p class="text-red-800 font-medium">{{ session('error') }}</p>
            <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif

    @if($showForm)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-40 flex items-center justify-center p-4" wire:click.self="resetForm">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-auto overflow-hidden flex flex-col max-h-[90vh]" @click.stop>
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                    <h2 class="text-xl font-bold text-slate-800">{{ $formMode === 'create' ? 'Nueva Reserva' : 'Editar Reserva' }}</h2>
                    <button wire:click="resetForm" class="text-slate-400 hover:text-slate-600 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto">
                    <form wire:submit.prevent="save" class="space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Check-in</label>
                                <input type="date" wire:model.live="check_in" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @error('check_in') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Check-out</label>
                                <input type="date" wire:model.live="check_out" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @error('check_out') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Cliente</label>
                                <select wire:model="client_id" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                                    <option value="">Seleccionar cliente</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}">{{ $client->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('client_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Habitación</label>
                                <select wire:model.live="room_id" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                                    <option value="">Seleccionar habitación</option>
                                    
                                    @if(count($availableRooms) > 0)
                                        <optgroup label="✅ Disponibles">
                                            @foreach($availableRooms as $room)
                                                <option value="{{ $room->id }}">Hab. {{ $room->numero }} - {{ ucfirst($room->tipo) }} (${{ number_format($room->precio_noche, 2) }})</option>
                                            @endforeach
                                        </optgroup>
                                    @endif

                                    @if(count($occupiedRooms) > 0)
                                        <optgroup label="❌ Ocupadas / Reservadas">
                                            @foreach($occupiedRooms as $room)
                                                <option value="{{ $room->id }}" disabled>Hab. {{ $room->numero }} - {{ ucfirst($room->tipo) }} (No disponible)</option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                </select>
                                @error('room_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Estado de Reserva</label>
                                <select wire:model="estado_reserva" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                                    <option value="pendiente">Pendiente</option>
                                    <option value="confirmada">Confirmada</option>
                                    <option value="completada">Completada</option>
                                    <option value="cancelada">Cancelada</option>
                                </select>
                                @error('estado_reserva') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        @if($room_id && count($servicios_disponibles) > 0)
                            <div class="pt-4 border-t border-slate-200">
                                <label class="block text-sm font-bold text-slate-800 mb-3">Servicios Solicitados para la Estancia</label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-40 overflow-y-auto pr-2">
                                    @foreach($servicios_disponibles as $servicio)
                                        <label class="flex items-center space-x-3 p-2 hover:bg-slate-50 rounded border border-transparent hover:border-slate-200 cursor-pointer transition">
                                            <input type="checkbox" wire:model="servicios_seleccionados" value="{{ $servicio }}" class="rounded text-blue-600 focus:ring-blue-500 h-4 w-4">
                                            <span class="text-sm text-slate-700">{{ $servicio }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @elseif($room_id && count($servicios_disponibles) === 0)
                            <div class="pt-4 border-t border-slate-200">
                                <p class="text-sm text-slate-500 italic">Esta habitación no tiene servicios adicionales registrados.</p>
                            </div>
                        @endif
                    </form>
                </div>

                <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 flex justify-end gap-3">
                    <button type="button" wire:click="resetForm" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-100 font-medium transition">Cancelar</button>
                    <button type="button" wire:click="save" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition shadow-sm flex items-center gap-2" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="save">Guardar Reserva</span>
                        <span wire:loading wire:target="save">Guardando...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Buscar Cliente o Habitación</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" placeholder="Ej. Juan o Hab. 101" wire:model.live.debounce.300ms="search" class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-slate-50">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Filtrar por Estado</label>
                <select wire:model.live="estado" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-slate-50">
                    <option value="">Todos los estados</option>
                    <option value="confirmada">Confirmada</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="completada">Completada</option>
                    <option value="cancelada">Cancelada</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Filtrar por Fecha</label>
                <input type="date" wire:model.live="fecha" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-slate-50">
            </div>
        </div>

        <div class="overflow-x-auto border border-slate-200 rounded-lg">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">ID</th>
                        <th class="px-6 py-4">Cliente</th>
                        <th class="px-6 py-4">Habitación</th>
                        <th class="px-6 py-4">Check-in</th>
                        <th class="px-6 py-4">Check-out</th>
                        <th class="px-6 py-4">Total</th>
                        <th class="px-6 py-4">Estado</th>
                        <th class="px-6 py-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($reservations as $reservation)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 text-slate-900 font-medium">#{{ $reservation->id }}</td>
                            <td class="px-6 py-4 text-slate-900 font-medium">{{ $reservation->client->nombre ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-slate-900">Hab. {{ $reservation->room->numero ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $reservation->fecha_inicio ? \Carbon\Carbon::parse($reservation->fecha_inicio)->format('d M Y') : 'N/A' }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $reservation->fecha_fin ? \Carbon\Carbon::parse($reservation->fecha_fin)->format('d M Y') : 'N/A' }}</td>
                            <td class="px-6 py-4 text-slate-900 font-bold">${{ number_format($reservation->precio_total ?? 0, 2) }}</td>
                            <td class="px-6 py-4">
                                <select wire:change="updateReservationStatus({{ $reservation->id }}, $event.target.value)" 
                                        class="w-full px-2 py-1.5 border rounded-lg text-xs font-semibold focus:ring-2 focus:ring-blue-500 cursor-pointer
                                        @if($reservation->estado === 'confirmada') bg-green-50 text-green-700 border-green-200
                                        @elseif($reservation->estado === 'pendiente') bg-amber-50 text-amber-700 border-amber-200
                                        @elseif($reservation->estado === 'completada') bg-blue-50 text-blue-700 border-blue-200
                                        @else bg-red-50 text-red-700 border-red-200
                                        @endif">
                                    <option value="pendiente" @selected($reservation->estado === 'pendiente')>Pendiente</option>
                                    <option value="confirmada" @selected($reservation->estado === 'confirmada')>Confirmada</option>
                                    <option value="completada" @selected($reservation->estado === 'completada')>Completada</option>
                                    <option value="cancelada" @selected($reservation->estado === 'cancelada')>Cancelada</option>
                                </select>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-3">
                                    <button wire:click="openEditForm({{ $reservation->id }})" title="Editar" class="text-blue-600 hover:text-blue-800 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    @if($reservation->estado !== 'cancelada' && $reservation->estado !== 'completada')
                                        <button wire:click="cancelReservation({{ $reservation->id }})" title="Cancelar Reserva" onclick="return confirm('¿Estás seguro de cancelar esta reserva?')" class="text-red-500 hover:text-red-700 transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <p class="font-medium text-slate-600">No hay reservas registradas</p>
                                    <p class="text-sm mt-1">Ajusta los filtros de búsqueda o crea una nueva reserva.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reservations->hasPages())
            <div class="mt-4">
                {{ $reservations->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
</div>