<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImageController;
use App\Livewire\ReservationDashboard;
use App\Livewire\ClientManager;
use App\Livewire\RoomManager;
use App\Livewire\ImageManager;
use App\Livewire\PaymentManager;
use App\Livewire\ReportGenerator;
use App\Livewire\SettingsManager;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    })->name('home');

    Route::get('/login', [LoginController::class, 'showLoginForm'])
        ->name('login')
        ->middleware('guest');

    Route::post('/login', [LoginController::class, 'login'])
        ->name('login.post')
        ->middleware('guest');

    Route::post('/logout', [LoginController::class, 'logout'])
        ->name('logout')
        ->middleware('auth');
});

Route::middleware(['web', 'auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/admin/reservas', ReservationDashboard::class)
        ->name('admin.reservations');

    Route::get('/admin/clientes', ClientManager::class)
        ->name('admin.clients');

    Route::get('/admin/habitaciones', RoomManager::class)
        ->name('admin.rooms');

    Route::get('/admin/imagenes', ImageManager::class)
        ->name('admin.images');

    Route::get('/admin/pagos', PaymentManager::class)
        ->name('admin.payments');

    Route::get('/admin/reportes', ReportGenerator::class)
        ->name('admin.reports');

    Route::get('/admin/configuracion', SettingsManager::class)
        ->name('admin.settings');

    Route::get('/hotel/visualizacion-3d', function () {
        return view('hotel.3d-rooms');
    })->name('hotel.visualization');
});

Route::redirect('/index.php', '/');