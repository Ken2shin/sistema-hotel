<header class="bg-white/80 backdrop-blur-xl border-b border-slate-200 shadow-sm sticky top-0 z-40">
    <div class="flex items-center justify-between px-6 py-3">
        <!-- Botón Móvil y Reloj -->
        <div class="flex items-center gap-4">
            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-slate-500 hover:text-blue-600 transition focus:outline-none">
                <i class="fa-solid fa-bars text-xl"></i>
            </button>
            <span class="hidden md:block text-slate-400 text-sm font-medium">
                <i class="fa-regular fa-clock mr-1"></i> <?php echo e(date('d M Y')); ?>

            </span>
        </div>

        <!-- Acciones: Búsqueda y Notificaciones -->
        <div class="flex items-center space-x-4">
            
            <!-- BUSCADOR GLOBAL -->
            <div class="relative hidden sm:block group" x-data="{ open: <?php if ((object) ('search') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('search'->value()); ?>')<?php echo e('search'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('search'); ?>')<?php endif; ?>.live.length > 0 }" @click.outside="open = false">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-slate-400 group-focus-within:text-blue-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                
                <input type="search" 
                    wire:model.live.debounce.300ms="search" 
                    @focus="if($wire.search.length > 0) open = true"
                    placeholder="Búsqueda rápida..." 
                    class="pl-10 pr-4 py-2.5 rounded-2xl bg-slate-100/80 text-slate-900 border border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 w-72 transition-all text-sm font-medium placeholder-slate-400">
                
                <!-- Spinner de carga -->
                <div wire:loading wire:target="search" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <svg class="animate-spin h-4 w-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </div>

                <!-- Resultados Desplegables -->
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($searchResults) > 0): ?>
                    <div x-show="open" x-transition x-cloak class="absolute top-full mt-3 w-[400px] right-0 sm:right-auto sm:left-0 bg-white rounded-3xl shadow-[0_10px_40px_rgb(0,0,0,0.1)] border border-slate-100 overflow-hidden z-50">
                        <div class="px-4 py-2 bg-slate-50/50 border-b border-slate-100 text-xs font-bold text-slate-400 uppercase tracking-wider">Resultados</div>
                        <ul class="max-h-[60vh] overflow-y-auto">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $searchResults; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li>
                                    <a href="<?php echo e($result['url']); ?>" class="flex items-center px-4 py-3 hover:bg-slate-50 transition-colors group border-b border-slate-50 last:border-0">
                                        <div class="w-10 h-10 rounded-full <?php echo e($result['icon_bg']); ?> flex items-center justify-center <?php echo e($result['icon_color']); ?> mr-4 group-hover:scale-110 transition-transform">
                                            <i class="fa-solid <?php echo e($result['icon']); ?>"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-800"><?php echo e($result['title']); ?></p>
                                            <p class="text-xs text-slate-500"><?php echo e($result['type']); ?> &bull; <?php echo e($result['subtitle']); ?></p>
                                        </div>
                                    </a>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </ul>
                    </div>
                <?php elseif(strlen($search) >= 2): ?>
                    <div x-show="open" x-transition x-cloak class="absolute top-full mt-3 w-72 right-0 sm:right-auto sm:left-0 bg-white rounded-3xl shadow-[0_10px_40px_rgb(0,0,0,0.1)] border border-slate-100 p-4 text-center z-50">
                        <p class="text-sm text-slate-500 font-medium">No se encontraron resultados para "<?php echo e($search); ?>"</p>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <!-- CAMPANA DE NOTIFICACIONES -->
            <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                <button @click="open = !open" class="relative p-2.5 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($unreadNotifications > 0): ?>
                        <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white animate-pulse"></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </button>

                <!-- Panel Desplegable de Notificaciones -->
                <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-cloak class="absolute top-full right-0 mt-3 w-80 bg-white rounded-3xl shadow-[0_10px_40px_rgb(0,0,0,0.1)] border border-slate-100 overflow-hidden z-50">
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                        <h4 class="font-extrabold text-slate-800 text-sm">Notificaciones</h4>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($unreadNotifications > 0): ?>
                            <button wire:click="clearNotifications" class="text-xs text-blue-600 hover:text-blue-800 font-bold bg-blue-50 px-2 py-1 rounded-lg">Marcar leídas</button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    
                    <div class="max-h-80 overflow-y-auto">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($unreadNotifications > 0): ?>
                            <div class="px-5 py-4 hover:bg-slate-50 border-b border-slate-50 transition cursor-pointer">
                                <p class="text-sm text-slate-800 font-semibold mb-1"><span class="w-2 h-2 inline-block bg-blue-500 rounded-full mr-2"></span>Nueva reserva detectada</p>
                                <p class="text-xs text-slate-500 ml-4">Cliente Carlos M. acaba de realizar una reserva.</p>
                                <p class="text-[10px] text-slate-400 font-medium ml-4 mt-1">Hace 5 minutos</p>
                            </div>
                            <div class="px-5 py-4 hover:bg-slate-50 border-b border-slate-50 transition cursor-pointer">
                                <p class="text-sm text-slate-800 font-semibold mb-1"><span class="w-2 h-2 inline-block bg-emerald-500 rounded-full mr-2"></span>Pago Procesado</p>
                                <p class="text-xs text-slate-500 ml-4">La transacción TRX-4521 se completó con éxito.</p>
                                <p class="text-[10px] text-slate-400 font-medium ml-4 mt-1">Hace 1 hora</p>
                            </div>
                        <?php else: ?>
                            <div class="px-5 py-8 text-center">
                                <svg class="w-12 h-12 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                <p class="text-sm font-medium text-slate-500">Estás al día</p>
                                <p class="text-xs text-slate-400">No tienes notificaciones pendientes.</p>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    
                    <a href="#" class="block w-full text-center px-4 py-3 bg-slate-50 text-xs font-bold text-slate-600 hover:text-blue-600 transition">
                        Ver todo el historial
                    </a>
                </div>
            </div>
            
        </div>
    </div>
</header><?php /**PATH C:\Users\EMANUELCLEMENTEMARTI\Downloads\sistema hotel\resources\views/livewire/global-header.blade.php ENDPATH**/ ?>