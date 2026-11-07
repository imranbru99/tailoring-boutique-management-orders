<?php

use App\Http\Controllers\Api\V1\AttendanceController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\InventoryController;
use App\Http\Controllers\Api\V1\JobCardController;
use App\Http\Controllers\Api\V1\MeasurementController;
use App\Http\Controllers\Api\V1\OrderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| REST API v1 Routes
|--------------------------------------------------------------------------
*/
Route::prefix('v1')->group(function () {
    // Public Authentication
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Protected API Endpoints (Sanctum)
    Route::middleware('auth:sanctum')->group(function () {
        // Authenticated User Identity
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        // Customers
        Route::get('/customers', [CustomerController::class, 'index']);
        Route::post('/customers', [CustomerController::class, 'store']);
        Route::get('/customers/{id}', [CustomerController::class, 'show']);

        // Measurements
        Route::get('/customers/{id}/measurements', [MeasurementController::class, 'index']);
        Route::post('/customers/{id}/measurements', [MeasurementController::class, 'store']);

        // Orders
        Route::get('/orders', [OrderController::class, 'index']);
        Route::post('/orders', [OrderController::class, 'store']);
        Route::get('/orders/{id}', [OrderController::class, 'show']);
        Route::post('/orders/{id}/status', [OrderController::class, 'updateStatus']);

        // Workshop Production & Job Cards
        Route::get('/job-cards', [JobCardController::class, 'index']);
        Route::get('/job-cards/{id}', [JobCardController::class, 'show']);
        Route::post('/job-cards/{id}/status', [JobCardController::class, 'updateStatus']);

        // Inventory
        Route::get('/inventory', [InventoryController::class, 'index']);
        Route::post('/inventory/adjust', [InventoryController::class, 'adjust']);

        // Staff Attendance
        Route::post('/attendance/punch', [AttendanceController::class, 'punch']);
        Route::get('/attendance/today', [AttendanceController::class, 'today']);
    });
});
