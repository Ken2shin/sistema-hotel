<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Generador de Reportes</h1>
            <p class="text-slate-600 mt-1">Crea y gestiona reportes del hotel</p>
        </div>
        <button wire:click="$set('showForm', true)" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition">
            Nuevo Reporte
        </button>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session()->has('message')): ?>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 flex items-center justify-between">
            <p class="text-green-800 font-medium"><?php echo e(session('message')); ?></p>
            <button @click="this.parentElement.remove()" class="text-green-600 hover:text-green-800">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session()->has('error')): ?>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 flex items-center justify-between">
            <p class="text-red-800 font-medium"><?php echo e(session('error')); ?></p>
            <button @click="this.parentElement.remove()" class="text-red-600 hover:text-red-800">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showForm): ?>
        <div class="bg-white rounded-lg border border-slate-200 p-6 shadow-sm">
            <h3 class="text-lg font-bold text-slate-900 mb-4">Crear Nuevo Reporte</h3>
            <form wire:submit.prevent="generateReport" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Nombre del Reporte</label>
                        <input type="text" wire:model="reportName" placeholder="Ej: Reporte de Ingresos Marzo" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['reportName'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-600 text-xs mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['startDate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-600 text-xs mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Fecha Fin</label>
                        <input type="date" wire:model="endDate" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['endDate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-600 text-xs mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
                <div class="flex gap-3 pt-4 border-t">
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="generateReport">Generar Reporte</span>
                        <span wire:loading wire:target="generateReport">Generando y Analizando...</span>
                    </button>
                    <button type="button" wire:click="$set('showForm', false)" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200">Cancelar</button>
                </div>
            </form>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="bg-white rounded-lg border border-slate-200 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-slate-700">Nombre</th>
                        <th class="px-6 py-3 text-left font-semibold text-slate-700">Tipo</th>
                        <th class="px-6 py-3 text-left font-semibold text-slate-700">Período</th>
                        <th class="px-6 py-3 text-left font-semibold text-slate-700">Estado</th>
                        <th class="px-6 py-3 text-right font-semibold text-slate-700">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 text-slate-900 font-medium">
                                <?php echo e($report->name); ?>

                                <div class="text-xs text-slate-500 font-normal mt-0.5">Creado el <?php echo e($report->created_at?->format('d/m/Y H:i')); ?></div>
                            </td>
                            <td class="px-6 py-4 text-slate-700">
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                    <?php echo e(match($report->type) {
                                        'revenue' => 'Ingresos',
                                        'occupancy' => 'Ocupación',
                                        'clients' => 'Clientes',
                                        'rooms' => 'Habitaciones',
                                        default => ucfirst($report->type)
                                    }); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-700">
                                <?php echo e($report->start_date?->format('d M, Y')); ?><br>
                                <span class="text-slate-400 text-xs">hasta</span> <?php echo e($report->end_date?->format('d M, Y')); ?>

                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                    <?php if(str_contains($report->status, 'generated') || str_contains($report->status, 'exported')): ?> bg-green-100 text-green-800
                                    <?php elseif($report->status === 'pending'): ?> bg-amber-100 text-amber-800
                                    <?php elseif($report->status === 'failed'): ?> bg-red-100 text-red-800
                                    <?php else: ?> bg-slate-100 text-slate-800
                                    <?php endif; ?>">
                                    <?php echo e($report->status === 'generated' ? 'Listo' : ucfirst($report->status)); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($report->status === 'generated' || str_contains($report->status, 'exported')): ?>
                                        <button wire:click="viewReport(<?php echo e($report->id); ?>)" class="text-slate-600 hover:text-blue-600 font-medium flex items-center gap-1" title="Ver Datos">
                                            <i class="fa-regular fa-eye"></i> Ver
                                        </button>
                                        <button wire:click="exportReport(<?php echo e($report->id); ?>)" class="text-slate-600 hover:text-emerald-600 font-medium flex items-center gap-1" title="Exportar Documento">
                                            <i class="fa-solid fa-download"></i> Exportar
                                        </button>
                                    <?php else: ?>
                                        <button wire:click="retryReport(<?php echo e($report->id); ?>)" class="text-amber-600 hover:text-amber-700 font-medium flex items-center gap-1" title="Procesar Datos">
                                            <i class="fa-solid fa-rotate-right"></i> Procesar
                                        </button>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <button wire:click="deleteReport(<?php echo e($report->id); ?>)" onclick="return confirm('¿Eliminar este reporte permanentemente?')" class="text-slate-400 hover:text-red-600 ml-2" title="Eliminar">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                <i class="fa-regular fa-folder-open text-4xl mb-3 text-slate-300"></i>
                                <p class="text-base font-medium text-slate-700">No hay reportes disponibles</p>
                                <p class="text-sm mt-1">Genera tu primer reporte usando el botón superior.</p>
                            </td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($reports->hasPages()): ?>
                <div class="px-6 py-4 border-t border-slate-200">
                    <?php echo e($reports->links()); ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showViewModal && $viewingReport): ?>
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-900 bg-opacity-75 transition-opacity" wire:click="closeViewModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex justify-between items-start border-b pb-3 mb-4">
                            <div>
                                <h3 class="text-xl leading-6 font-bold text-slate-900" id="modal-title">
                                    <?php echo e($viewingReport->name); ?>

                                </h3>
                                <p class="text-sm text-slate-500 mt-1">
                                    Resultados del <?php echo e($viewingReport->start_date?->format('d/m/Y')); ?> al <?php echo e($viewingReport->end_date?->format('d/m/Y')); ?>

                                </p>
                            </div>
                            <button wire:click="closeViewModal" class="text-slate-400 hover:text-slate-600">
                                <i class="fa-solid fa-xmark text-xl"></i>
                            </button>
                        </div>
                        
                        <div class="bg-slate-50 rounded p-4 max-h-[60vh] overflow-y-auto">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($viewingReport->data && is_array($viewingReport->data)): ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $viewingReport->data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section => $values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="mb-6 last:mb-0">
                                        <h4 class="font-bold text-slate-800 uppercase text-xs tracking-wider mb-3 pb-1 border-b"><?php echo e(str_replace('_', ' ', $section)); ?></h4>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(is_array($values)): ?>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $values; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <div class="bg-white p-3 rounded border border-slate-200 shadow-sm">
                                                        <div class="text-xs text-slate-500 mb-1 font-bold"><?php echo e(str_replace('_', ' ', ucfirst($key))); ?></div>
                                                        
                                                        
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(is_array($val)): ?>
                                                            <div class="mt-2 space-y-1">
                                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $val; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subKey => $subVal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <div class="flex justify-between items-center text-sm border-b border-slate-50 pb-1 last:border-0">
                                                                        <span class="text-slate-500"><?php echo e(str_replace('_', ' ', ucfirst($subKey))); ?>:</span>
                                                                        <span class="font-medium text-slate-900"><?php echo e(is_numeric($subVal) ? number_format($subVal, 2) : $subVal); ?></span>
                                                                    </div>
                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                            </div>
                                                        <?php else: ?>
                                                            <div class="font-semibold text-slate-900 text-lg"><?php echo e(is_numeric($val) ? number_format($val, 2) : $val); ?></div>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        <?php else: ?>
                                            <p class="text-slate-700 font-medium"><?php echo e($values); ?></p>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php else: ?>
                                <p class="text-slate-500 text-center py-4">No hay datos estructurados disponibles para este reporte.</p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t">
                        <button wire:click="exportReport(<?php echo e($viewingReport->id); ?>)" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm">
                            Descargar Reporte
                        </button>
                        <button wire:click="closeViewModal" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-slate-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showExportModal && $selectedReport): ?>
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-900 bg-opacity-75 transition-opacity" wire:click="closeExportModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-bold text-slate-900 mb-4" id="modal-title">
                            Exportar: <?php echo e(Str::limit($selectedReport->name, 30)); ?>

                        </h3>
                        <div class="space-y-4">
                            <p class="text-sm text-slate-500">Selecciona el formato en el que deseas descargar el documento. El PDF está formateado para presentaciones empresariales.</p>
                            
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Formato de Archivo</label>
                                <select wire:model="exportFormat" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="pdf">Documento PDF (Recomendado)</option>
                                    <option value="csv">Archivo de Excel (CSV)</option>
                                    <option value="json">Datos Crudos (JSON)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t">
                        <button wire:click="doExport" class="w-full inline-flex justify-center items-center gap-2 rounded-md border border-transparent shadow-sm px-4 py-2 bg-emerald-600 text-base font-medium text-white hover:bg-emerald-700 sm:ml-3 sm:w-auto sm:text-sm">
                            <i class="fa-solid fa-download"></i> Descargar Ahora
                        </button>
                        <button wire:click="closeExportModal" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-slate-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div><?php /**PATH C:\Users\EMANUELCLEMENTEMARTI\Downloads\sistema hotel\resources\views/livewire/report-generator.blade.php ENDPATH**/ ?>