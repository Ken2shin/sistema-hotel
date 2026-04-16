<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RoomVisualizationController;
use App\Http\Controllers\Api\ImageController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SettingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['throttle:10,1'])->group(function () {
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
});

Route::middleware(['auth:sanctum', 'throttle:100,1'])->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    
    Route::middleware(['throttle:60,1'])->group(function () {
        Route::get('/usuarios', [UserController::class, 'index']);
        Route::post('/usuarios', [UserController::class, 'store']);
        Route::put('/usuarios/{id}', [UserController::class, 'update']);
        Route::get('/roles', [UserController::class, 'getRoles']);
    });
    
    Route::middleware(['throttle:40,1'])->group(function () {
        Route::get('/reportes', [ReportController::class, 'index']);
        Route::post('/reportes', [ReportController::class, 'store']);
        Route::get('/reportes/{report}', [ReportController::class, 'show']);
        Route::post('/reportes/{report}/generate', [ReportController::class, 'generate']);
        Route::post('/reportes/{report}/export', [ReportController::class, 'export']);
        Route::delete('/reportes/{report}', [ReportController::class, 'delete']);
    });

    Route::middleware(['throttle:50,1'])->group(function () {
        Route::get('/settings', [SettingController::class, 'index']);
        Route::get('/settings/{key}', [SettingController::class, 'show']);
        Route::put('/settings/{key}', [SettingController::class, 'update']);
        Route::post('/settings/bulk', [SettingController::class, 'bulk']);
        Route::post('/settings/{key}/reset', [SettingController::class, 'reset']);
    });
});

Route::middleware(['throttle:40,1'])->group(function () {
    Route::get('/clientes', [ClientController::class, 'index']);
    Route::post('/clientes', [ClientController::class, 'store']);
    Route::get('/clientes/{client}', [ClientController::class, 'show']);
    Route::put('/clientes/{client}', [ClientController::class, 'update']);
    Route::delete('/clientes/{client}', [ClientController::class, 'destroy']);
    Route::post('/clientes/{client}/reservations', [ClientController::class, 'reservations']);

    Route::get('/habitaciones', [RoomController::class, 'index']);
    Route::get('/habitaciones/{room}', [RoomController::class, 'show']);
    Route::get('/habitaciones/disponibles', [RoomController::class, 'available']);
    Route::get('/habitaciones/{id}/resenas', [RoomController::class, 'reviews']);
    Route::get('/habitaciones/{id}/rating', [RoomController::class, 'rating']);

    Route::post('/reservas', [ReservationController::class, 'store']);
    Route::get('/reservas', [ReservationController::class, 'index']);
    Route::get('/reservas/{reservation}', [ReservationController::class, 'show']);
    Route::put('/reservas/{reservation}', [ReservationController::class, 'update']);
    Route::post('/reservas/{reservation}/cancel', [ReservationController::class, 'cancel']);
    Route::get('/reservas/cliente/{clientId}', [ReservationController::class, 'clientReservations']);
    Route::get('/disponibles/check', [ReservationController::class, 'available']);

    Route::post('/pagos', [PaymentController::class, 'store']);
    Route::get('/resenas', [ReviewController::class, 'index']);
    Route::post('/resenas', [ReviewController::class, 'store']);

    Route::post('/habitaciones/{room}/imagenes', [ImageController::class, 'uploadRoomImage']);
    Route::get('/habitaciones/{room}/imagenes', [ImageController::class, 'getRoomImages']);
    Route::delete('/imagenes/{id}', [ImageController::class, 'deleteImage']);
    Route::put('/imagenes/{id}/principal', [ImageController::class, 'setPrimaryImage']);
    
    Route::post('/habitaciones/{room}/360-images', [ImageController::class, 'upload360Image']);
    Route::get('/habitaciones/{room}/360-images', [ImageController::class, 'get360Images']);
    Route::delete('/360-images/{image}', [ImageController::class, 'delete360Image']);
    Route::post('/360-images/{image}/primary', [ImageController::class, 'setPrimary360Image']);
    Route::post('/360-images/reorder/{room}', [ImageController::class, 'reorder360Images']);

    Route::get('/hotel/floor-plan', [RoomVisualizationController::class, 'getHotelFloorPlan']);
    Route::get('/hotel/room/{id}/state', [RoomVisualizationController::class, 'getRoomState']);
});
