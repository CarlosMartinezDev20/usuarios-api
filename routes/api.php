<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StatisticsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aquí se registran todas las rutas API de la aplicación.
| Estas rutas son cargadas por el RouteServiceProvider y están asignadas
| al grupo de middleware "api".
|
*/

// Rutas públicas (no requieren autenticación)
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
});

// Rutas protegidas (requieren autenticación con Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    
    // Rutas de autenticación
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::post('/refresh', [AuthController::class, 'refresh'])->name('auth.refresh');
        Route::get('/me', [AuthController::class, 'me'])->name('auth.me');
        Route::post('/logout-all', [AuthController::class, 'logoutAll'])->name('auth.logoutAll');
    });

    // Rutas de usuarios CRUD
    Route::apiResource('users', UserController::class);
    
    // Rutas adicionales de usuarios
    Route::prefix('users')->group(function () {
        Route::get('/trashed/list', [UserController::class, 'trashed'])->name('users.trashed');
        Route::post('/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
    });

    // Rutas de estadísticas
    Route::prefix('statistics')->group(function () {
        Route::get('/', [StatisticsController::class, 'index'])->name('statistics.index');
        Route::get('/daily', [StatisticsController::class, 'daily'])->name('statistics.daily');
        Route::get('/weekly', [StatisticsController::class, 'weekly'])->name('statistics.weekly');
        Route::get('/monthly', [StatisticsController::class, 'monthly'])->name('statistics.monthly');
    });
});

// Ruta de salud de la API
Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'API funcionando correctamente',
        'timestamp' => now()->toDateTimeString()
    ]);
})->name('api.health');
