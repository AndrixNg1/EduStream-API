<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CourseController;

// API v1 pour AUTH
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

// APi v1 pour course
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('courses', CourseController::class);
});
