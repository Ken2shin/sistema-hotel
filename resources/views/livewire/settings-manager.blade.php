<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-slate-900">Configuración del Sistema</h1>
        <p class="text-slate-600 mt-1">Administra las configuraciones del hotel</p>
    </div>

    @if(session()->has('message'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 flex items-center justify-between">
            <p class="text-green-800">{{ session('message') }}</p>
            <button @click="this.parentElement.remove()" class="text-green-600 hover:text-green-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif

    @if($showForm && $editingKey)
        <div class="bg-white rounded-lg border border-slate-200 p-6">
            <h3 class="text-lg font-bold text-slate-900 mb-4">Editar Configuración</h3>
            <form wire:submit.prevent="saveSetting" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Clave</label>
                    <input type="text" wire:model="editingKey" readonly class="w-full px-4 py-2 border border-slate-300 rounded-lg bg-slate-50 text-slate-600">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Valor</label>
                    <textarea wire:model="editingValue" placeholder="Ingresa el valor" rows="3" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    @error('editingValue') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Tipo</label>
                    <select wire:model="editingType" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="string">Texto</option>
                        <option value="boolean">Booleano</option>
                        <option value="integer">Entero</option>
                        <option value="decimal">Decimal</option>
                        <option value="array">Array</option>
                        <option value="json">JSON</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Descripción</label>
                    <input type="text" wire:model="editingDescription" placeholder="Descripción de esta configuración" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex gap-3 pt-4 border-t">
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700" wire:loading.attr="disabled">
                        {{ $isSaving ? 'Guardando...' : 'Guardar' }}
                    </button>
                    <button type="button" wire:click="resetForm" class="px-4 py-2 bg-slate-300 text-slate-900 rounded-lg hover:bg-slate-400">Cancelar</button>
                </div>
            </form>
        </div>
    @endif

    <div class="bg-white rounded-lg border border-slate-200 p-6">
        <div class="mb-6">
            <input type="text" placeholder="Buscar configuración..." wire:model.live="searchQuery" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-slate-700">Clave</th>
                        <th class="px-6 py-3 text-left font-semibold text-slate-700">Descripción</th>
                        <th class="px-6 py-3 text-left font-semibold text-slate-700">Valor</th>
                        <th class="px-6 py-3 text-left font-semibold text-slate-700">Tipo</th>
                        <th class="px-6 py-3 text-center font-semibold text-slate-700">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($filteredSettings as $setting)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-3 text-slate-900 font-medium">{{ $setting['key'] }}</td>
                            <td class="px-6 py-3 text-slate-600 text-sm">{{ $setting['description'] ?? '-' }}</td>
                            <td class="px-6 py-3 text-slate-700">
                                @if($setting['is_encrypted'])
                                    <span class="text-amber-600">🔒 [ENCRIPTADO]</span>
                                @else
                                    <code class="bg-slate-100 px-2 py-1 rounded text-xs">{{ Str::limit($setting['value'], 50) }}</code>
                                @endif
                            </td>
                            <td class="px-6 py-3">
                                <span class="inline-block px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-700">
                                    {{ ucfirst($setting['type']) }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-center space-x-2">
                                <button wire:click="openEditForm('{{ $setting['key'] }}')" class="text-blue-600 hover:text-blue-800 font-medium text-sm">Editar</button>
                                <button wire:click="deleteSetting('{{ $setting['key'] }}')" onclick="return confirm('¿Estás seguro?')" class="text-red-600 hover:text-red-800 font-medium text-sm">Eliminar</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-600">
                                <p class="text-sm">No hay configuraciones que coincidan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
