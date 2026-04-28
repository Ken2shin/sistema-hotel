<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Administración Central</h1>
            <p class="text-slate-500 mt-1 text-sm">Gestiona variables de entorno y accesos de usuarios</p>
        </div>
        
        <div>
            @if($activeTab === 'settings')
                <button wire:click="openEditForm" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition shadow-sm flex items-center gap-2">
                    <i class="fa-solid fa-code"></i> Nueva Variable
                </button>
            @else
                <button wire:click="openUserForm" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-medium transition shadow-sm flex items-center gap-2">
                    <i class="fa-solid fa-user-plus"></i> Alta de Empleado
                </button>
            @endif
        </div>
    </div>

    @if(session()->has('message'))
        <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-4 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3 text-emerald-800">
                <i class="fa-regular fa-circle-check text-xl"></i>
                <p class="font-medium">{{ session('message') }}</p>
            </div>
            <button @click="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 transition">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
    @endif
    @if(session()->has('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3 text-red-800">
                <i class="fa-solid fa-triangle-exclamation text-xl"></i>
                <p class="font-medium">{{ session('error') }}</p>
            </div>
            <button @click="this.parentElement.remove()" class="text-red-600 hover:text-red-800 transition">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
    @endif

    <div class="border-b border-slate-200">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button wire:click="switchTab('settings')" 
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'settings' ? 'border-blue-500 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                <i class="fa-solid fa-sliders mr-2"></i> Variables del Sistema
            </button>
            <button wire:click="switchTab('users')" 
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'users' ? 'border-blue-500 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                <i class="fa-solid fa-users-gear mr-2"></i> Control de Accesos (Usuarios)
            </button>
        </nav>
    </div>

    @if($activeTab === 'settings')
        @if($showSettingForm)
            <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm mb-6">
                <div class="flex items-center justify-between mb-5 border-b border-slate-100 pb-3">
                    <h3 class="text-lg font-bold text-slate-800">{{ $editingKey ? 'Modificar Variable' : 'Registrar Variable' }}</h3>
                    <button wire:click="resetForm" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>
                <form wire:submit.prevent="saveSetting" class="space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Clave (Key)</label>
                            <input type="text" wire:model="editingKey" {{ $editingKey ? 'readonly' : '' }} class="w-full px-4 py-2 border border-slate-300 rounded-lg {{ $editingKey ? 'bg-slate-100 text-slate-500 cursor-not-allowed' : 'bg-white' }}">
                            @error('editingKey') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Tipo de Dato</label>
                            <select wire:model="editingType" class="w-full px-4 py-2 border border-slate-300 rounded-lg">
                                <option value="string">Cadena de Texto</option>
                                <option value="boolean">Booleano</option>
                                <option value="integer">Entero</option>
                                <option value="json">JSON / Array</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Valor</label>
                        <textarea wire:model="editingValue" rows="3" class="w-full px-4 py-2 border border-slate-300 rounded-lg font-mono text-sm"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Descripción</label>
                        <input type="text" wire:model="editingDescription" class="w-full px-4 py-2 border border-slate-300 rounded-lg">
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" wire:click="resetForm" class="px-5 py-2 text-slate-600 bg-slate-100 rounded-lg hover:bg-slate-200 font-medium">Cancelar</button>
                        <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium" wire:loading.attr="disabled">Guardar</button>
                    </div>
                </form>
            </div>
        @endif

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-200 bg-slate-50">
                <input type="text" placeholder="Buscar variable..." wire:model.live.debounce.300ms="searchQuery" class="w-full md:w-96 px-4 py-2 border border-slate-300 rounded-lg">
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-white border-b border-slate-200 text-slate-600 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-4 font-bold">Clave</th>
                            <th class="px-6 py-4 font-bold">Valor Asignado</th>
                            <th class="px-6 py-4 font-bold text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($settings as $setting)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4 font-mono text-slate-800">{{ $setting['key'] }}<br><span class="text-xs text-slate-400 font-sans">{{ $setting['description'] }}</span></td>
                                <td class="px-6 py-4 font-mono text-xs text-slate-600">{{ is_array($setting['value']) ? '{ Estructura JSON }' : Str::limit($setting['value'], 50) }}</td>
                                <td class="px-6 py-4 text-right">
                                    <button wire:click="openEditForm('{{ $setting['key'] }}')" class="text-blue-600 hover:text-blue-800 mr-3"><i class="fa-solid fa-pen-to-square text-lg"></i></button>
                                    <button wire:click="deleteSetting('{{ $setting['key'] }}')" class="text-red-400 hover:text-red-600"><i class="fa-regular fa-trash-can text-lg"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($settings->hasPages()) <div class="px-6 py-4 bg-slate-50 border-t">{{ $settings->links() }}</div> @endif
        </div>
    @endif

    @if($activeTab === 'users')
        
        @if($generatedPassword)
            <div class="bg-amber-50 border border-amber-300 rounded-xl p-6 mb-6 shadow-sm">
                <h3 class="text-amber-800 font-bold text-lg mb-2"><i class="fa-solid fa-key"></i> Clave Generada Exitosamente</h3>
                <p class="text-amber-700 text-sm mb-4">Entrega esta credencial al empleado. <strong>Por motivos de encriptación, el sistema no volverá a mostrar esta contraseña.</strong></p>
                <div class="bg-white border border-amber-200 p-4 rounded-lg flex flex-col sm:flex-row items-center justify-between gap-4">
                    <code class="text-2xl font-mono text-slate-800 tracking-widest font-bold">{{ $generatedPassword }}</code>
                    <button wire:click="$set('generatedPassword', null)" class="px-6 py-2 bg-amber-600 text-white rounded hover:bg-amber-700 font-medium transition">Confirmar y Cerrar</button>
                </div>
            </div>
        @endif

        @if($showUserForm && !$generatedPassword)
            <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm mb-6 border-t-4 border-t-emerald-500">
                <div class="flex items-center justify-between mb-5 border-b border-slate-100 pb-3">
                    <h3 class="text-lg font-bold text-slate-800">Alta de Nuevo Empleado</h3>
                    <button wire:click="resetForm" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>
                
                <form wire:submit.prevent="saveUser" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Nombre Completo</label>
                            <input type="text" wire:model="userName" placeholder="Ej: Juan Pérez" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            @error('userName') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Correo (Acceso ERP)</label>
                            <input type="email" wire:model="userEmail" placeholder="juan.perez@hotel.com" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            @error('userEmail') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Plantilla de Permisos (Rol)</label>
                            <select wire:model="userRole" {{ $customPermissions ? 'disabled' : '' }} class="w-full px-4 py-2 border border-slate-300 rounded-lg bg-white disabled:bg-slate-100 disabled:text-slate-400">
                                <option value="">-- Seleccionar Rol Base --</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ strtoupper($role->name) }}</option>
                                @endforeach
                            </select>
                            @error('userRole') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex items-center pt-6">
                            <label class="flex items-center cursor-pointer">
                                <div class="relative">
                                    <input type="checkbox" wire:model.live="customPermissions" class="sr-only">
                                    <div class="block {{ $customPermissions ? 'bg-blue-600' : 'bg-slate-300' }} w-14 h-8 rounded-full transition"></div>
                                    <div class="dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition {{ $customPermissions ? 'transform translate-x-6' : '' }}"></div>
                                </div>
                                <div class="ml-3">
                                    <span class="text-slate-700 font-bold">Permisos Personalizados</span>
                                    <p class="text-xs text-slate-500">Habilitar selección manual de módulos</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    @if($customPermissions)
                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-5">
                            <label class="block text-sm font-bold text-slate-800 mb-3 border-b border-slate-200 pb-2">Módulos Habilitados para este Empleado</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                @foreach($this->availableModules as $key => $module)
                                    <label class="relative flex items-center p-3 cursor-pointer rounded-lg border {{ in_array($key, $selectedModules) ? 'border-blue-500 bg-blue-50' : 'border-slate-200 bg-white' }} hover:shadow-sm transition-all">
                                        <input type="checkbox" wire:model="selectedModules" value="{{ $key }}" class="w-4 h-4 text-blue-600 bg-white border-slate-300 rounded focus:ring-blue-500">
                                        <div class="ml-3 text-sm">
                                            <span class="font-bold text-slate-700 block"><i class="fa-solid {{ $module['icon'] }} text-blue-500 w-5"></i> {{ $module['label'] }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            @if(empty($selectedModules)) <span class="text-red-600 text-xs mt-2 block font-medium"><i class="fa-solid fa-circle-exclamation"></i> Debes marcar al menos un módulo.</span> @endif
                        </div>
                    @endif

                    <div class="flex items-center justify-between border-t border-slate-100 pt-5">
                        <div class="text-sm text-slate-500"><i class="fa-solid fa-shield-halved text-emerald-600"></i> Se generará una contraseña encriptada automáticamente.</div>
                        <div class="flex gap-3">
                            <button type="button" wire:click="resetForm" class="px-5 py-2 text-slate-600 bg-slate-100 rounded-lg hover:bg-slate-200 font-medium transition">Cancelar</button>
                            <button type="submit" class="px-5 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-medium transition flex items-center gap-2" wire:loading.attr="disabled">
                                <i class="fa-solid fa-user-check" wire:loading.remove wire:target="saveUser"></i> Guardar Empleado
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        @endif

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
                <input type="text" placeholder="Buscar empleado o correo..." wire:model.live.debounce.300ms="searchQuery" class="w-full md:w-96 px-4 py-2 border border-slate-300 rounded-lg">
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-white border-b border-slate-200 text-slate-600 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-4 font-bold">Información del Empleado</th>
                            <th class="px-6 py-4 font-bold">Rol en Sistema</th>
                            <th class="px-6 py-4 font-bold">Estado</th>
                            <th class="px-6 py-4 font-bold text-right">Acción Segura</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($users as $user)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 rounded-full bg-slate-800 flex items-center justify-center text-white font-bold">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-bold text-slate-900">{{ $user->name }}</div>
                                            <div class="text-xs text-slate-500 font-mono">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ str_contains(strtolower($user->role->name ?? ''), 'personalizado') ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ strtoupper($user->role->name ?? 'Sin Asignar') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($user->is_active)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-emerald-50 border border-emerald-200 text-emerald-700"><div class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></div> Activo</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-red-50 border border-red-200 text-red-700"><div class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></div> Suspendido</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($user->id !== auth()->id())
                                        <button wire:click="toggleUserStatus({{ $user->id }})" onclick="return confirm('¿Confirmas el cambio de estado para este empleado?')" class="text-sm font-bold transition px-3 py-1.5 border rounded-md {{ $user->is_active ? 'text-amber-700 border-amber-200 bg-amber-50 hover:bg-amber-100' : 'text-emerald-700 border-emerald-200 bg-emerald-50 hover:bg-emerald-100' }}">
                                            {{ $user->is_active ? 'Bloquear Acceso' : 'Permitir Acceso' }}
                                        </button>
                                    @else
                                        <span class="text-xs text-slate-400 font-bold bg-slate-100 px-2 py-1 rounded">CUENTA PROPIA</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($users->hasPages()) <div class="px-6 py-4 bg-slate-50 border-t">{{ $users->links() }}</div> @endif
        </div>
    @endif
</div>