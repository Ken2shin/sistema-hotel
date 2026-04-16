<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Generador de Reportes</h1>
            <p class="text-slate-600 mt-1">Crea y gestiona reportes del hotel</p>
        </div>
        <button wire:click="$set('showForm', true)" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
            Nuevo Reporte
        </button>
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

    @if($showForm)
        <div class="bg-white rounded-lg border border-slate-200 p-6">
            <h3 class="text-lg font-bold text-slate-900 mb-4">Crear Nuevo Reporte</h3>
            <form wire:submit.prevent="generateReport" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Nombre del Reporte</label>
                        <input type="text" wire:model="reportName" placeholder="Ej: Reporte de Ingresos Marzo" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('reportName') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Tipo de Reporte</label>
                        <select wire:model="reportType" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="revenue">Ingresos</option>
                            <option value="occupancy">Ocupación</option>
                            <option value="clients">Clientes</option>
                            <option value="rooms">Habitaciones</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Frecuencia</label>
                        <select wire:model="frequency" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="daily">Diario</option>
                            <option value="weekly">Semanal</option>
                            <option value="monthly">Mensual</option>
                            <option value="custom">Personalizado</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Fecha Inicio</label>
                        <input type="date" wire:model="startDate" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('startDate') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Fecha Fin</label>
                        <input type="date" wire:model="endDate" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('endDate') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="flex gap-3 pt-4 border-t">
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700" wire:loading.attr="disabled">
                        {{ $isGenerating ? 'Generando...' : 'Generar Reporte' }}
                    </button>
                    <button type="button" wire:click="$set('showForm', false)" class="px-4 py-2 bg-slate-300 text-slate-900 rounded-lg hover:bg-slate-400">Cancelar</button>
                </div>
            </form>
        </div>
    @endif

    <div class="bg-white rounded-lg border border-slate-200 p-6">
        <div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-200">
            <tr>
                <th class="px-6 py-3 text-left font-semibold text-slate-700">Nombre</th>
                <th class="px-6 py-3 text-left font-semibold text-slate-700">Tipo</th>
                <th class="px-6 py-3 text-left font-semibold text-slate-700">Frecuencia</th>
                <th class="px-6 py-3 text-left font-semibold text-slate-700">Estado</th>
                <th class="px-6 py-3 text-left font-semibold text-slate-700">Período</th>
                <th class="px-6 py-3 text-left font-semibold text-slate-700">Creado</th>
                <th class="px-6 py-3 text-left font-semibold text-slate-700">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
            @forelse($reports as $report)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-6 py-3 text-slate-900 font-medium">{{ $report->name }}</td>
                    <td class="px-6 py-3 text-slate-700">
                        <span class="inline-block px-2 py-1 rounded text-xs font-medium bg-slate-100">
                            {{ match($report->type) {
                                'revenue' => 'Ingresos',
                                'occupancy' => 'Ocupación',
                                'clients' => 'Clientes',
                                'rooms' => 'Habitaciones',
                                default => ucfirst($report->type)
                            } }}
                        </span>
                    </td>
                    <td class="px-6 py-3 text-slate-700">
                        {{ match($report->frequency) {
                            'daily' => 'Diario',
                            'weekly' => 'Semanal',
                            'monthly' => 'Mensual',
                            'quarterly' => 'Trimestral',
                            'yearly' => 'Anual',
                            default => ucfirst($report->frequency)
                        } }}
                    </td>
                    <td class="px-6 py-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                            @if($report->status === 'generated') bg-green-100 text-green-700
                            @elseif($report->status === 'pending') bg-amber-100 text-amber-700
                            @else bg-slate-100 text-slate-700
                            @endif">
                            {{ match($report->status) {
                                'generated' => 'Generado',
                                'pending' => 'Pendiente',
                                'processing' => 'Procesando',
                                default => ucfirst($report->status)
                            } }}
                        </span>
                    </td>
                    <td class="px-6 py-3 text-slate-700 text-sm">
                        {{ $report->start_date?->format('d M') }} - {{ $report->end_date?->format('d M Y') }}
                    </td>
                    <td class="px-6 py-3 text-slate-600 text-sm">{{ $report->created_at?->format('d M Y') }}</td>
                    <td class="px-6 py-3">
                        <div class="flex items-center gap-2">
                            @if($report->status === 'pending')
                                <button wire:click="generateReport({{ $report->id }})" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                    Generar
                                </button>
                            @endif
                            @if($report->status === 'generated')
                                <button wire:click="downloadReport({{ $report->id }}, 'csv')" class="text-green-600 hover:text-green-700 text-sm font-medium">
                                    Descargar
                                </button>
                            @endif
                            <button wire:click="deleteReport({{ $report->id }})" onclick="return confirm('¿Estás seguro?')" class="text-red-600 hover:text-red-700 text-sm font-medium">
                                Eliminar
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-slate-600">
                        <p class="text-sm">Sin reportes creados</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($reports->hasPages())
        <div class="px-6 py-4 border-t border-slate-200 flex items-center justify-between">
            <div class="text-sm text-slate-600">
                Mostrando {{ $reports->firstItem() ?? 0 }} a {{ $reports->lastItem() ?? 0 }} de {{ $reports->total() }}
            </div>
            <div class="flex gap-2">
                @if($reports->onFirstPage())
                    <span class="px-4 py-2 border border-slate-300 text-slate-500 rounded-lg">Anterior</span>
                @else
                    <button wire:click="previousPage()" class="px-4 py-2 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition">Anterior</button>
                @endif

                @if($reports->hasMorePages())
                    <button wire:click="nextPage()" class="px-4 py-2 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition">Siguiente</button>
                @else
                    <span class="px-4 py-2 border border-slate-300 text-slate-500 rounded-lg">Siguiente</span>
                @endif
            </div>
        </div>
    @endif
        </div>
    </div>
</div>
