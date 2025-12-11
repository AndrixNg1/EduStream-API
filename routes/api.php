<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\LocalStreamController;
use App\Http\Controllers\ProgressController;
use App\Models\LessonStream;

/*
|--------------------------------------------------------------------------
| API Routes v1
|--------------------------------------------------------------------------
|
| Version 1 of the API. All routes are prefixed with /api/v1
| Authentication is handled via Sanctum where required.
|
*/

Route::prefix('v1')->group(function () {

    /**
     * ------------------------------------------------------------------------
     * AUTH
     * ------------------------------------------------------------------------
     */
    // Public
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);

    // Protected
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/me', [AuthController::class, 'me']);
    });

    /**
     * ------------------------------------------------------------------------
     * COURSES
     * ------------------------------------------------------------------------
     */
    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('courses', CourseController::class);
    });

    /**
     * ------------------------------------------------------------------------
     * CHAPTERS
     * ------------------------------------------------------------------------
     */
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('courses/{course}/chapters', [ChapterController::class, 'index']);
        Route::post('courses/{course}/chapters', [ChapterController::class, 'store']);
        Route::get('chapters/{chapter}', [ChapterController::class, 'show']);
        Route::put('chapters/{chapter}', [ChapterController::class, 'update']);
        Route::delete('chapters/{chapter}', [ChapterController::class, 'destroy']);
    });

    /**
     * ------------------------------------------------------------------------
     * LESSONS
     * ------------------------------------------------------------------------
     */
    Route::get('chapters/{chapter}/lessons', [LessonController::class, 'index']);
    Route::post('chapters/{chapter}/lessons', [LessonController::class, 'store']);
    Route::get('lessons/{lesson}', [LessonController::class, 'show']);
    Route::put('lessons/{lesson}', [LessonController::class, 'update']);
    Route::delete('lessons/{lesson}', [LessonController::class, 'destroy']);

    // Streaming endpoint (protected)
    Route::get('lessons/{lesson}/stream', [LessonController::class, 'stream']);

    /**
     * ------------------------------------------------------------------------
     * LOCAL LESSON STREAM (SIGNED URL)
     * ------------------------------------------------------------------------
     */
    Route::get('local/lesson-stream/{streamId}', [LocalStreamController::class, 'stream'])
         ->name('local.lesson.stream');

    /**
     * ------------------------------------------------------------------------
     * OPTIONAL: Nested API Resource for Chapters -> Lessons
     * ------------------------------------------------------------------------
     * This allows `chapters/{chapter}/lessons` RESTful endpoints
     */
    Route::apiResource('chapters.lessons', LessonController::class)
         ->only(['index', 'store', 'show', 'update', 'destroy']);

    Route::middleware('auth:sanctum')->group(function () {
    Route::post('lessons/{lesson}/progress', [ProgressController::class, 'update']);
    Route::get('courses/{course}/progress', [ProgressController::class, 'courseProgress']);
});

});
