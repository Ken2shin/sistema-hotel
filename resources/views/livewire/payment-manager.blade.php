<div class="space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Gestión de Pagos</h1>
            <p class="text-slate-600 mt-1">Registra y administra todos los pagos</p>
        </div>
        <button wire:click="openCreateForm" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Registrar Nuevo Pago
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

    <div class="flex flex-col md:flex-row gap-3 bg-white p-4 rounded-lg border border-slate-200 shadow-sm">
        <div class="flex-1">
            <input type="text" placeholder="Buscar por cliente o referencia TRX..." wire:model.live.debounce.300ms="search" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50">
        </div>
        <select wire:model.live="status" class="w-full md:w-48 px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50">
            <option value="">Todos los estados</option>
            <option value="completado">Completado</option>
            <option value="pendiente">Pendiente</option>
            <option value="fallido">Fallido</option>
            <option value="reembolsado">Reembolsado</option>
        </select>
    </div>

    @if($showForm)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center bg-slate-50">
                    <h2 class="text-xl font-bold text-slate-800">Registrar Nuevo Pago</h2>
                    <button wire:click="closeForm" class="text-slate-400 hover:text-slate-600 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Seleccionar Reserva</label>
                        <select wire:model="reservation_id" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Elija una reserva activa --</option>
                            @foreach($pendingReservations as $res)
                                <option value="{{ $res->id }}">Reserva #{{ $res->id }} - {{ $res->client->nombre ?? 'Sin cliente' }} (Total: ${{ $res->precio_total ?? 0 }})</option>
                            @endforeach
                        </select>
                        @error('reservation_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Monto a Pagar ($)</label>
                            <input type="number" step="0.01" wire:model="monto" placeholder="0.00" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('monto') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Método de Pago</label>
                            <select wire:model="metodo_pago" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="efectivo">Efectivo</option>
                                <option value="tarjeta_credito">Tarjeta de Crédito</option>
                                <option value="tarjeta_debito">Tarjeta de Débito</option>
                                <option value="transferencia">Transferencia Bancaria</option>
                                <option value="paypal">PayPal / Digital</option>
                            </select>
                            @error('metodo_pago') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Descripción / Notas (Opcional)</label>
                        <textarea wire:model="descripcion" rows="2" placeholder="Ej. Pago adelantado, abono inicial..." class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                        @error('descripcion') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 flex justify-end gap-3">
                    <button wire:click="closeForm" class="px-4 py-2 text-slate-600 hover:text-slate-800 font-medium">Cancelar</button>
                    <button wire:click="savePayment" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium shadow-sm flex items-center gap-2" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="savePayment">Procesar Pago</span>
                        <span wire:loading wire:target="savePayment">Procesando...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">ID Transacción</th>
                        <th class="px-6 py-4">Cliente / Reserva</th>
                        <th class="px-6 py-4">Monto</th>
                        <th class="px-6 py-4">Método</th>
                        <th class="px-6 py-4">Estado</th>
                        <th class="px-6 py-4">Fecha</th>
                        <th class="px-6 py-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 font-mono text-slate-800 text-xs">
                                {{ $payment->numero_transaccion ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-medium text-slate-900">{{ $payment->reservation->client->nombre ?? 'N/A' }}</p>
                                <p class="text-xs text-slate-500">Reserva #{{ $payment->reservation_id }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-900">${{ number_format($payment->monto, 2) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded text-xs font-medium border border-slate-200">
                                    {{ ucwords(str_replace('_', ' ', $payment->metodo_pago)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border
                                    @if($payment->estado === 'completado') bg-green-50 text-green-700 border-green-200
                                    @elseif($payment->estado === 'pendiente') bg-amber-50 text-amber-700 border-amber-200
                                    @elseif($payment->estado === 'fallido') bg-red-50 text-red-700 border-red-200
                                    @else bg-blue-50 text-blue-700 border-blue-200
                                    @endif">
                                    {{ ucfirst($payment->estado) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-600 text-sm">
                                {{ $payment->fecha_pago ? \Carbon\Carbon::parse($payment->fecha_pago)->format('d M, Y H:i') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-3">
                                    @if($payment->estado === 'pendiente')
                                        <button wire:click="markAsCompleted({{ $payment->id }})" title="Marcar como completado" class="text-green-600 hover:text-green-800 transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        </button>
                                    @endif
                                    <button wire:click="deletePayment({{ $payment->id }})" onclick="return confirm('¿Eliminar permanentemente este registro de pago?')" title="Eliminar" class="text-red-500 hover:text-red-700 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    <p class="text-slate-600 font-medium">No se encontraron pagos</p>
                                    <p class="text-sm mt-1">Registra un nuevo pago para que aparezca aquí.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($payments->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                {{ $payments->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
</div>