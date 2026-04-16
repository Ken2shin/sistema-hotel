<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Gestión de Clientes</h1>
            <p class="text-slate-600 mt-1">Administra todos los clientes registrados</p>
        </div>
        <button wire:click="openCreateForm" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
            Nuevo Cliente
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
        <input type="text" placeholder="Buscar por nombre o email..." wire:model.live="search" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 mb-6">

        @if($showForm)
            <div class="mb-6 p-6 bg-slate-50 border border-slate-200 rounded-lg">
                <h3 class="text-lg font-bold text-slate-900 mb-4">{{ $formMode === 'create' ? 'Nuevo Cliente' : 'Editar Cliente' }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Nombre</label>
                        <input type="text" wire:model="nombre" placeholder="Nombre completo" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('nombre') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                        <input type="email" wire:model="email" placeholder="email@example.com" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('email') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Teléfono</label>
                        <input type="text" wire:model="phone" placeholder="Teléfono" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('phone') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Documento</label>
                        <input type="text" wire:model="document" placeholder="Documento" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('document') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Ciudad</label>
                        <input type="text" wire:model="city" placeholder="Ciudad" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('city') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">País</label>
                        <input type="text" wire:model="country" placeholder="País" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('country') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Dirección</label>
                        <input type="text" wire:model="address" placeholder="Dirección completa" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('address') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Tipo de Cliente</label>
                        <select wire:model="client_type" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="regular">Regular</option>
                            <option value="vip">VIP</option>
                            <option value="corporate">Corporativo</option>
                        </select>
                        @error('client_type') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="flex gap-3 mt-4">
                    <button wire:click="save" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="save">Guardar</span>
                        <span wire:loading wire:target="save">Guardando...</span>
                    </button>
                    <button wire:click="resetForm" class="px-4 py-2 bg-slate-300 text-slate-900 rounded-lg hover:bg-slate-400 transition font-medium">Cancelar</button>
                </div>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-slate-700">Nombre</th>
                        <th class="px-6 py-3 text-left font-semibold text-slate-700">Email</th>
                        <th class="px-6 py-3 text-left font-semibold text-slate-700">Teléfono</th>
                        <th class="px-6 py-3 text-left font-semibold text-slate-700">Documento</th>
                        <th class="px-6 py-3 text-left font-semibold text-slate-700">Ciudad</th>
                        <th class="px-6 py-3 text-left font-semibold text-slate-700">Tipo</th>
                        <th class="px-6 py-3 text-center font-semibold text-slate-700">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($clients as $client)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-3 text-slate-900 font-medium">{{ $client->nombre ?? 'N/A' }}</td>
                            <td class="px-6 py-3 text-slate-600">{{ $client->email ?? 'N/A' }}</td>
                            <td class="px-6 py-3 text-slate-900">{{ $client->phone ?? 'N/A' }}</td>
                            <td class="px-6 py-3 text-slate-900">{{ $client->document ?? 'N/A' }}</td>
                            <td class="px-6 py-3 text-slate-900">{{ $client->city ?? 'N/A' }}</td>
                            <td class="px-6 py-3">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                    {{ match($client->client_type ?? 'regular') {
                                        'vip' => 'VIP',
                                        'corporate' => 'Corporativo',
                                        default => 'Regular'
                                    } }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-center space-x-2">
                                <button wire:click="openEditForm({{ $client->id }})" class="text-blue-600 hover:text-blue-800 font-medium text-sm">Editar</button>
                                <button wire:click="delete({{ $client->id }})" onclick="return confirm('¿Estás seguro de eliminar este cliente?')" class="text-red-600 hover:text-red-800 font-medium text-sm">Eliminar</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-slate-600">
                                <p class="text-sm">No hay clientes registrados</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($clients, 'links'))
            <div class="mt-6">
                {{ $clients->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
</div>