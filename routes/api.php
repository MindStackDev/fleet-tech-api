<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Admin\AdminDashboardController;
use App\Http\Controllers\Api\V1\Admin\AdminOperatorController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Checklists\TemplateController;
use App\Http\Controllers\Api\V1\Defects\DefectController;
use App\Http\Controllers\Api\V1\Fleet\VehicleController;
use App\Http\Controllers\Api\V1\Fleet\VehicleTypeController;
use App\Http\Controllers\Api\V1\Inspections\InspectionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - FleetTech Enterprise V1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // ------------------------------------------
    // Autenticación Pública
    // ------------------------------------------
    Route::post('/auth/login', [AuthController::class, 'login']);

    // ------------------------------------------
    // Rutas Protegidas (Laravel Sanctum)
    // ------------------------------------------
    Route::middleware('auth:sanctum')->group(function () {
        // Auth & Perfil
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        // Flota & Vehículos de Patio
        Route::get('/vehicle-types', [VehicleTypeController::class, 'index']);
        Route::get('/vehicles', [VehicleController::class, 'index']);
        Route::get('/vehicles/{id}', [VehicleController::class, 'show']);
        Route::post('/vehicles', [VehicleController::class, 'store']);

        // Plantillas Dinámicas de Checklist
        Route::get('/checklist-templates', [TemplateController::class, 'index']);
        Route::get('/checklist-templates/{id}', [TemplateController::class, 'show']);

        // Inspecciones de Patio & Relevo de Turno
        Route::get('/inspections', [InspectionController::class, 'index']);
        Route::get('/inspections/{id}', [InspectionController::class, 'show']);
        Route::post('/inspections', [InspectionController::class, 'store']);

        // Defectos & Taller
        Route::get('/defects', [DefectController::class, 'index']);
        Route::get('/defects/{id}', [DefectController::class, 'show']);
        Route::patch('/defects/{id}/resolve', [DefectController::class, 'resolve']);

        // ------------------------------------------
        // Rutas Exclusivas de Administrador
        // ------------------------------------------
        Route::middleware('role.admin')->prefix('admin')->group(function () {
            // Actividad diaria y resumen de métricas
            Route::get('/daily-activity', [AdminDashboardController::class, 'dailyActivity']);

            // Gestión de Operadores
            Route::get('/operators', [AdminOperatorController::class, 'index']);
            Route::post('/operators', [AdminOperatorController::class, 'store']);
            Route::get('/operators/{id}', [AdminOperatorController::class, 'show']);
            Route::put('/operators/{id}', [AdminOperatorController::class, 'update']);
            Route::patch('/operators/{id}/toggle-status', [AdminOperatorController::class, 'toggleStatus']);
            Route::delete('/operators/{id}', [AdminOperatorController::class, 'destroy']);
        });
    });
});
