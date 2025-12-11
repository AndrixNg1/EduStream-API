<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ChapterController;

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

Route::middleware('auth:sanctum')->group(function () {
    // chapters
    Route::get('courses/{course}/chapters', [ChapterController::class, 'index']);
    Route::post('courses/{course}/chapters', [ChapterController::class, 'store']);

    Route::get('chapters/{chapter}', [ChapterController::class, 'show']);
    Route::put('chapters/{chapter}', [ChapterController::class, 'update']);
    Route::delete('chapters/{chapter}', [ChapterController::class, 'destroy']);
});
