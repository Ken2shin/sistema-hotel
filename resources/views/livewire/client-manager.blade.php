<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Gestión de Clientes</h1>
            <p class="text-slate-500 mt-1.5 font-medium text-sm">Administra el directorio empresarial de clientes</p>
        </div>
        <button wire:click="openCreateForm" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl transition-all shadow-[0_8px_20px_rgb(37,99,235,0.2)] hover:shadow-[0_8px_25px_rgb(37,99,235,0.3)] hover:-translate-y-0.5 font-bold text-sm flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Nuevo Cliente
        </button>
    </div>

    @if(session()->has('success'))
        <div class="bg-emerald-50/80 backdrop-blur-md border border-emerald-100 rounded-2xl p-4 flex items-center justify-between shadow-sm animate-fade-in-down">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <p class="text-emerald-800 font-semibold text-sm">{{ session('success') }}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    @endif

    @if(session()->has('error'))
        <div class="bg-red-50/80 backdrop-blur-md border border-red-100 rounded-2xl p-4 flex items-center justify-between shadow-sm animate-fade-in-down">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <p class="text-red-800 font-semibold text-sm">{{ session('error') }}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    @endif

    <div class="bg-white/80 backdrop-blur-xl rounded-3xl border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-6">
        
        <div class="relative mb-6">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" placeholder="Buscar por nombre o email..." wire:model.live.debounce.300ms="search" class="w-full pl-11 pr-10 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all text-sm font-medium text-slate-700 placeholder-slate-400">
            <div wire:loading wire:target="search" class="absolute inset-y-0 right-0 pr-4 flex items-center">
                <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            </div>
        </div>

        @if($showForm)
            <div class="mb-8 p-7 bg-slate-50/80 rounded-3xl border border-slate-200/60 shadow-sm transition-all">
                <h3 class="text-xl font-extrabold text-slate-800 mb-5">{{ $formMode === 'create' ? 'Registrar Nuevo Cliente' : 'Actualizar Datos del Cliente' }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nombre Completo</label>
                        <input type="text" wire:model="nombre" placeholder="Ej. Carlos Martínez" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all text-sm font-medium">
                        @error('nombre') <span class="text-red-500 text-xs font-bold mt-1.5 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Correo Electrónico</label>
                        <input type="email" wire:model="email" placeholder="email@empresa.com" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all text-sm font-medium">
                        @error('email') <span class="text-red-500 text-xs font-bold mt-1.5 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Teléfono</label>
                        <input type="text" wire:model="phone" placeholder="+505 0000-0000" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all text-sm font-medium">
                        @error('phone') <span class="text-red-500 text-xs font-bold mt-1.5 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Documento / Cédula</label>
                        <input type="text" wire:model="document" placeholder="000-000000-0000A" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all text-sm font-medium">
                        @error('document') <span class="text-red-500 text-xs font-bold mt-1.5 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Ciudad</label>
                        <input type="text" wire:model="city" placeholder="Ej. Chinandega" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all text-sm font-medium">
                        @error('city') <span class="text-red-500 text-xs font-bold mt-1.5 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">País</label>
                        <input type="text" wire:model="country" placeholder="Nicaragua" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all text-sm font-medium">
                        @error('country') <span class="text-red-500 text-xs font-bold mt-1.5 block">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Dirección Exacta</label>
                        <input type="text" wire:model="address" placeholder="Dirección completa" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all text-sm font-medium">
                        @error('address') <span class="text-red-500 text-xs font-bold mt-1.5 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Clasificación</label>
                        <select wire:model="client_type" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all text-sm font-medium">
                            <option value="regular">Regular</option>
                            <option value="vip">VIP</option>
                            <option value="corporate">Corporativo</option>
                        </select>
                        @error('client_type') <span class="text-red-500 text-xs font-bold mt-1.5 block">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="flex gap-3 mt-8">
                    <button wire:click="save" class="px-6 py-2.5 bg-slate-900 text-white rounded-xl hover:bg-slate-800 transition shadow-md font-bold text-sm flex items-center justify-center min-w-[120px]" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="save">{{ $formMode === 'create' ? 'Guardar' : 'Actualizar' }}</span>
                        <span wire:loading wire:target="save">Procesando...</span>
                    </button>
                    <button wire:click="resetForm" class="px-6 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-xl hover:bg-slate-50 transition font-bold text-sm">Cancelar</button>
                </div>
            </div>
        @endif

        <div class="overflow-x-auto rounded-2xl border border-slate-100">
            <table class="w-full text-sm">
                <thead class="bg-slate-50/50">
                    <tr>
                        <th class="px-6 py-4 text-left font-bold text-slate-500 tracking-wide">Nombre</th>
                        <th class="px-6 py-4 text-left font-bold text-slate-500 tracking-wide">Contacto</th>
                        <th class="px-6 py-4 text-left font-bold text-slate-500 tracking-wide">Documento</th>
                        <th class="px-6 py-4 text-left font-bold text-slate-500 tracking-wide">Ubicación</th>
                        <th class="px-6 py-4 text-left font-bold text-slate-500 tracking-wide">Tipo</th>
                        <th class="px-6 py-4 text-right font-bold text-slate-500 tracking-wide">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100/60 bg-white">
                    @forelse($clients as $client)
                        <tr class="hover:bg-slate-50/50 transition duration-200 group">
                            <td class="px-6 py-4 text-slate-800 font-bold">{{ $client->nombre ?? 'N/A' }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-slate-800 font-medium">{{ $client->email ?? 'N/A' }}</span>
                                    <span class="text-slate-500 text-xs">{{ $client->telefono ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600 font-mono text-xs">{{ $client->cedula ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $client->ciudad ?? 'N/A' }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $badgeStyle = match($client->tipo_cliente ?? 'regular') {
                                        'vip' => 'bg-purple-100 text-purple-700 border-purple-200',
                                        'corporate' => 'bg-blue-100 text-blue-700 border-blue-200',
                                        default => 'bg-slate-100 text-slate-700 border-slate-200'
                                    };
                                    $badgeText = match($client->tipo_cliente ?? 'regular') {
                                        'vip' => 'VIP',
                                        'corporate' => 'Corporativo',
                                        default => 'Regular'
                                    };
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $badgeStyle }}">
                                    {{ $badgeText }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-3 opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity">
                                <button wire:click="openEditForm({{ $client->id }})" class="text-blue-600 hover:text-blue-800 font-bold text-sm bg-blue-50 px-3 py-1.5 rounded-lg transition-colors">Editar</button>
                                <button wire:click="delete({{ $client->id }})" onclick="return confirm('¿Eliminar este registro permanentemente?')" class="text-red-600 hover:text-red-800 font-bold text-sm bg-red-50 px-3 py-1.5 rounded-lg transition-colors">Borrar</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-3">
                                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292m0 0H8.646m3.354 0H16m0 0a4 4 0 110-5.292m0 0H8.646m3.354 0H16"></path></svg>
                                    </div>
                                    <p class="text-slate-500 font-medium">No se encontraron clientes.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($clients->hasPages())
            <div class="mt-6 pt-4 border-t border-slate-100">
                {{ $clients->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
</div>