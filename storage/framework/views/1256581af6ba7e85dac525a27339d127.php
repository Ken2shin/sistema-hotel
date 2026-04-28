<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'HotelMS - Enterprise ERP'); ?></title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

    <?php echo $__env->yieldPushContent('styles'); ?>
    
    <style>
        [x-cloak] { display: none !important; }
        /* Estilos empresariales modernos para el scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        .sidebar-link { transition: all 0.2s ease-in-out; }
        .sidebar-link.active { 
            background-color: #2563eb; /* blue-600 */
            color: #ffffff;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
            font-weight: 600;
        }
        .sidebar-link:not(.active) {
            color: #94a3b8; /* slate-400 */
        }
        .sidebar-link:not(.active):hover {
            background-color: #1e293b; /* slate-800 */
            color: #f8fafc; /* slate-50 */
        }
    </style>
</head>

<?php
    // =========================================================================
    // MOTOR DE PERMISOS DINÁMICOS (POO)
    // Valida en tiempo real qué módulos puede ver el empleado logueado.
    // =========================================================================
    $user = auth()->user();
    $role = $user->role ?? null;
    
    // CORRECCIÓN A PRUEBA DE BALAS: 
    // Ahora verifica si el nombre del rol CONTIENE la palabra "admin" (ej: "Administrador").
    $isAdmin = $role && str_contains(strtolower(trim($role->name)), 'admin');

    // Extraemos y cacheamos en memoria los permisos del usuario para no saturar la BD
    $userPermissions = [];
    if ($user && !$isAdmin && $role) {
        $userPermissions = \Illuminate\Support\Facades\DB::table('role_permissions')
            ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
            ->where('role_permissions.role_id', $role->id)
            ->pluck('permissions.name')
            ->toArray();
    }

    // Closure (Función anónima) para validar el acceso limpio en el HTML
    $canAccess = function($module) use ($isAdmin, $userPermissions) {
        if ($isAdmin) return true; // El Admin ve todo por defecto por ley
        return in_array($module, $userPermissions);
    };
?>

<body class="bg-slate-50 text-slate-900 antialiased font-sans" x-data="{ sidebarOpen: window.innerWidth >= 1024 }" @keydown.escape="sidebarOpen = false">
    <div class="flex h-screen bg-slate-50 overflow-hidden">
        
        <div class="fixed lg:relative inset-y-0 left-0 z-50 w-64 bg-[#0f172a] shadow-2xl transform transition-transform duration-300 ease-in-out border-r border-slate-800"
             :class="{
                 'translate-x-0': sidebarOpen || window.innerWidth >= 1024,
                 '-translate-x-full lg:translate-x-0': !sidebarOpen && window.innerWidth < 1024,
             }"
             @click.outside="if(window.innerWidth < 1024) sidebarOpen = false">
            
            <div class="flex flex-col h-full">
                
                <div class="flex items-center justify-between px-6 py-5 border-b border-slate-800/60 bg-[#0f172a]">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 bg-blue-600 rounded-lg flex items-center justify-center shadow-lg shadow-blue-600/20">
                            <i class="fa-solid fa-building text-white text-lg"></i>
                        </div>
                        <div>
                            <h1 class="text-lg font-bold text-white tracking-wide leading-tight">HotelMS</h1>
                            <p class="text-[10px] text-blue-400 font-semibold tracking-widest uppercase">Enterprise</p>
                        </div>
                    </div>
                    <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white transition">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                <nav class="flex-1 overflow-y-auto px-3 py-6 space-y-1">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canAccess('dashboard')): ?>
                            <a href="<?php echo e(route('dashboard')); ?>" class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-lg <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
                                <i class="fa-solid fa-chart-pie w-5 text-center"></i>
                                <span class="text-sm">Dashboard</span>
                            </a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="pt-5 pb-2 px-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Gestión Operativa</div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canAccess('reservations')): ?>
                            <a href="<?php echo e(route('admin.reservations')); ?>" class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-lg <?php echo e(request()->routeIs('admin.reservations') ? 'active' : ''); ?>">
                                <i class="fa-solid fa-calendar-check w-5 text-center"></i>
                                <span class="text-sm">Reservas</span>
                            </a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canAccess('clients')): ?>
                            <a href="<?php echo e(route('admin.clients')); ?>" class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-lg <?php echo e(request()->routeIs('admin.clients') ? 'active' : ''); ?>">
                                <i class="fa-solid fa-users w-5 text-center"></i>
                                <span class="text-sm">Clientes</span>
                            </a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canAccess('rooms')): ?>
                            <a href="<?php echo e(route('admin.rooms')); ?>" class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-lg <?php echo e(request()->routeIs('admin.rooms') ? 'active' : ''); ?>">
                                <i class="fa-solid fa-bed w-5 text-center"></i>
                                <span class="text-sm">Habitaciones</span>
                            </a>
                            
                            <a href="<?php echo e(route('admin.images')); ?>" class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-lg ml-2 <?php echo e(request()->routeIs('admin.images') ? 'active' : ''); ?>">
                                <i class="fa-regular fa-images w-5 text-center text-xs"></i>
                                <span class="text-sm">Galería de Imágenes</span>
                            </a>
                            <a href="<?php echo e(route('hotel.visualization')); ?>" class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-lg ml-2 <?php echo e(request()->routeIs('hotel.visualization') ? 'active' : ''); ?>">
                                <i class="fa-solid fa-cube w-5 text-center text-xs"></i>
                                <span class="text-sm">Vista 3D</span>
                            </a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="pt-5 pb-2 px-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Administración</div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canAccess('payments')): ?>
                            <a href="<?php echo e(route('admin.payments')); ?>" class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-lg <?php echo e(request()->routeIs('admin.payments') ? 'active' : ''); ?>">
                                <i class="fa-solid fa-credit-card w-5 text-center"></i>
                                <span class="text-sm">Facturación y Pagos</span>
                            </a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canAccess('reports')): ?>
                            <a href="<?php echo e(route('admin.reports')); ?>" class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-lg <?php echo e(request()->routeIs('admin.reports') ? 'active' : ''); ?>">
                                <i class="fa-solid fa-file-invoice w-5 text-center"></i>
                                <span class="text-sm">Reportes Auditables</span>
                            </a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canAccess('settings')): ?>
                            <a href="<?php echo e(route('admin.settings')); ?>" class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-lg <?php echo e(request()->routeIs('admin.settings') ? 'active' : ''); ?>">
                                <i class="fa-solid fa-sliders w-5 text-center"></i>
                                <span class="text-sm">Configuración</span>
                            </a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </nav>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                    <div class="border-t border-slate-800 p-3 bg-[#0b1120]">
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="w-full flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-slate-800 transition">
                                <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-md flex items-center justify-center text-xs font-bold text-white shadow-sm border border-indigo-400/30">
                                    <?php echo e(strtoupper(substr(auth()->user()->name ?? 'U', 0, 1))); ?>

                                </div>
                                <div class="flex-1 text-left overflow-hidden">
                                    <p class="text-sm font-bold text-slate-200 truncate"><?php echo e(auth()->user()->name ?? 'Usuario'); ?></p>
                                    <p class="text-[11px] text-slate-400 font-mono truncate"><?php echo e(auth()->user()->role->name ?? 'Empleado'); ?></p>
                                </div>
                                <i class="fa-solid fa-chevron-up text-slate-500 text-xs transition-transform duration-200" :class="{ 'rotate-180': !open }"></i>
                            </button>
                            
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-cloak
                                 @click.outside="open = false" 
                                 class="absolute bottom-full left-0 right-0 mb-2 bg-slate-800 rounded-lg shadow-xl border border-slate-700 overflow-hidden z-50">
                                <form method="POST" action="<?php echo e(route('logout')); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="w-full text-left px-4 py-3 text-slate-300 hover:text-white hover:bg-red-600/90 flex items-center space-x-3 transition-colors">
                                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                                        <span class="text-sm font-semibold">Cerrar Sesión</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        <div class="flex-1 flex flex-col h-screen overflow-hidden">
            
            <header class="bg-white border-b border-slate-200 shadow-sm sticky top-0 z-40">
                <div class="flex items-center justify-between px-6 py-3">
                    <div class="flex items-center gap-4">
                        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-slate-500 hover:text-blue-600 transition focus:outline-none">
                            <i class="fa-solid fa-bars text-xl"></i>
                        </button>
                        <span class="hidden md:block text-slate-400 text-sm font-medium"><i class="fa-regular fa-clock mr-1"></i> <?php echo e(date('d M Y')); ?></span>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <div class="relative hidden sm:block group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-magnifying-glass text-slate-400 group-focus-within:text-blue-500 transition"></i>
                            </div>
                            <input type="search" placeholder="Búsqueda rápida..." class="pl-9 pr-4 py-2 rounded-lg bg-slate-100 text-slate-900 border border-transparent focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 w-64 transition-all text-sm">
                        </div>
                        
                        <button class="relative p-2.5 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                            <i class="fa-regular fa-bell text-lg"></i>
                            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
                        </button>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto bg-slate-50 relative">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
                    <?php echo $__env->yieldContent('content'); ?>
                    <?php echo e($slot ?? ''); ?> 
                </div>
            </main>

            <footer class="bg-white border-t border-slate-200 py-3 px-6 text-center text-xs text-slate-500 font-medium">
                <p>&copy; <?php echo e(date('Y')); ?> Hotel Management System ERP. Todos los derechos reservados.</p>
            </footer>
        </div>
    </div>

    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH C:\Users\EMANUELCLEMENTEMARTI\Downloads\sistema hotel\resources\views/layouts/app.blade.php ENDPATH**/ ?>