<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

// Optional Laravel default route
Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// API v1
Route::prefix('v1')->group(function () {

    // Public Auth Endpoints
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);

    // Protected Auth Endpoints
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/me', [AuthController::class, 'me']);
    });

});
