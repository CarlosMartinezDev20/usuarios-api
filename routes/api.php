<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StatisticsController;

/*
|--------------------------------------------------------------------------
| API Routes - Requisitos del Sistema
|--------------------------------------------------------------------------
|
| Endpoints esenciales que cumplen los requisitos:
| 1. CRUD de Usuarios (Crear, Leer, Actualizar, Eliminar)
| 2. Autenticación con token de 5 minutos
| 3. Estadísticas por día, semana y mes
|
*/

// ========================================
// AUTENTICACIÓN (Pública)
// ========================================
Route::prefix('auth')->group(function () {
    // Registro de nuevos usuarios → devuelve token de 5 minutos
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
    
    // Login de usuarios → devuelve token de 5 minutos
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
});

// ========================================
// RUTAS PROTEGIDAS (Requieren Token)
// ========================================
Route::middleware('auth:sanctum')->group(function () {
    
    // Gestión de Token
    Route::prefix('auth')->group(function () {
        Route::post('/refresh', [AuthController::class, 'refresh'])->name('auth.refresh'); // Refrescar token
        Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');     // Cerrar sesión
    });

    // ========================================
    // CRUD DE USUARIOS
    // ========================================
    Route::prefix('users')->group(function () {
        // CREAR usuario
        Route::post('/', [UserController::class, 'store'])->name('users.store');
        
        // LEER usuarios
        Route::get('/', [UserController::class, 'index'])->name('users.index');           // Lista todos
        Route::get('/{id}', [UserController::class, 'show'])->name('users.show');         // Ver uno
        
        // ACTUALIZAR usuario
        Route::put('/{id}', [UserController::class, 'update'])->name('users.update');
        
        // ELIMINAR usuario
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // ========================================
    // ESTADÍSTICAS
    // ========================================
    Route::prefix('statistics')->group(function () {
        // Estadísticas por DÍA (últimos 30 días)
        Route::get('/daily', [StatisticsController::class, 'daily'])->name('statistics.daily');
        
        // Estadísticas por SEMANA (últimas 12 semanas)
        Route::get('/weekly', [StatisticsController::class, 'weekly'])->name('statistics.weekly');
        
        // Estadísticas por MES (últimos 12 meses)
        Route::get('/monthly', [StatisticsController::class, 'monthly'])->name('statistics.monthly');
    });
});
