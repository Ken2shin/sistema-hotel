<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Gestión de Habitaciones</h1>
            <p class="text-slate-600 mt-1">Administra todas las habitaciones del hotel</p>
        </div>
        <button wire:click="openCreateForm" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
            Nueva Habitación
        </button>
    </div>

    @if(session()->has('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 flex items-center justify-between">
            <p class="text-green-800">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif

    @if(session()->has('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 flex items-center justify-between">
            <p class="text-red-800">{{ session('error') }}</p>
            <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif

    <div class="bg-white rounded-lg border border-slate-200 p-6">
        <input type="text" placeholder="Buscar por número o tipo..." wire:model.live="search" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 mb-6">

        @if($showForm)
            <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center" wire:click.self="resetForm">
                <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl mx-4 p-6 max-h-[90vh] overflow-y-auto" @click.stop>
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-slate-900">{{ $formMode === 'create' ? 'Nueva Habitación' : 'Editar Habitación' }}</h2>
                        <button wire:click="resetForm" class="text-slate-600 hover:text-slate-900">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="save" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Número</label>
                                <input type="text" wire:model="numero" placeholder="Ej: 101" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('numero') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Tipo</label>
                                <select wire:model="tipo" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Seleccionar tipo</option>
                                    <option value="single">Single</option>
                                    <option value="double">Double</option>
                                    <option value="triple">Triple</option>
                                    <option value="suite">Suite</option>
                                </select>
                                @error('tipo') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Capacidad</label>
                                <input type="number" wire:model="capacidad" placeholder="Personas" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('capacidad') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Precio Noche ($)</label>
                                <input type="number" step="0.01" wire:model="precio_noche" placeholder="0.00" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('precio_noche') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Precio Fin de Semana ($)</label>
                                <input type="number" step="0.01" wire:model="precio_fin_semana" placeholder="0.00" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('precio_fin_semana') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Estado</label>
                                <select wire:model="is_active" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="1">Disponible</option>
                                    <option value="0">Mantenimiento</option>
                                </select>
                                @error('is_active') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            
                            {{-- SECCIÓN DE SERVICIOS --}}
                            <div class="md:col-span-3">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Servicios Incluidos</label>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 p-4 bg-white border border-slate-300 rounded-lg max-h-40 overflow-y-auto">
                                    @foreach($serviciosDisponibles as $servicio)
                                        <label class="flex items-center space-x-2 cursor-pointer hover:bg-slate-50 p-1 rounded transition">
                                            <input type="checkbox" wire:model="servicios" value="{{ $servicio }}" class="rounded text-blue-600 focus:ring-blue-500 h-4 w-4">
                                            <span class="text-sm text-slate-700">{{ $servicio }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('servicios') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="md:col-span-3">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Imagen Principal (Opcional)</label>
                                <input type="file" wire:model="imagen" accept="image/*" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                @error('imagen') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
                                @if ($imagen)
                                    <div class="mt-2">
                                        <p class="text-xs text-slate-500 mb-1">Vista previa:</p>
                                        <img src="{{ $imagen->temporaryUrl() }}" class="h-32 object-cover rounded border border-slate-200">
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-sm font-medium text-slate-700 mb-2">Descripción</label>
                            <textarea wire:model="descripcion" placeholder="Descripción de la habitación" rows="3" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                            @error('descripcion') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t mt-4">
                            <button type="button" wire:click="resetForm" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50">Cancelar</button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="save">Guardar Habitación</span>
                                <span wire:loading wire:target="save">Guardando...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-slate-700">Número</th>
                        <th class="px-6 py-3 text-left font-semibold text-slate-700">Tipo</th>
                        <th class="px-6 py-3 text-left font-semibold text-slate-700">Capacidad</th>
                        <th class="px-6 py-3 text-left font-semibold text-slate-700">Precio Noche</th>
                        <th class="px-6 py-3 text-left font-semibold text-slate-700">Estado</th>
                        <th class="px-6 py-3 text-left font-semibold text-slate-700">Imagen</th>
                        <th class="px-6 py-3 text-center font-semibold text-slate-700">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($rooms as $room)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-3 text-slate-900 font-bold">{{ $room->numero ?? 'N/A' }}</td>
                            <td class="px-6 py-3 text-slate-900">{{ ucfirst($room->tipo ?? 'N/A') }}</td>
                            <td class="px-6 py-3 text-slate-900">{{ $room->capacidad ?? 'N/A' }} personas</td>
                            <td class="px-6 py-3 text-slate-900">${{ number_format($room->precio_noche ?? 0, 2) }}</td>
                            <td class="px-6 py-3">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $room->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $room->is_active ? 'Disponible' : 'Mantenimiento' }}
                                </span>
                            </td>
                            
                            {{-- COLUMNA DE IMAGEN MEJORADA --}}
                            <td class="px-6 py-3">
                                @php 
                                    // Busca la imagen principal a través de la relación del modelo
                                    $primaryImg = $room->images->where('is_primary', true)->first() ?? $room->images->first(); 
                                @endphp
                                
                                @if($primaryImg)
                                    <img src="{{ $primaryImg->path }}" alt="Imagen de Hab. {{ $room->numero }}" class="h-10 w-14 object-cover rounded border border-slate-200 shadow-sm">
                                @else
                                    <span class="text-slate-400 text-xs italic">Sin imagen</span>
                                @endif
                            </td>
                            
                            <td class="px-6 py-3 text-center space-x-2">
                                <button wire:click="openEditForm({{ $room->id }})" class="text-blue-600 hover:text-blue-800 font-medium text-sm">Editar</button>
                                <button wire:click="delete({{ $room->id }})" onclick="return confirm('¿Estás seguro de eliminar esta habitación?')" class="text-red-600 hover:text-red-800 font-medium text-sm">Eliminar</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-slate-600">
                                <p class="text-sm">No hay habitaciones registradas</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($rooms, 'links'))
            <div class="mt-6">
                {{ $rooms->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
</div>