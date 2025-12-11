<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ChapterController;
use App\Models\LessonStream;
use App\Http\Controllers\LessonController;

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


Route::get('/local/lesson-stream/{streamId}', function ($streamId, Request $request) {
    if (! $request->hasValidSignature()) {
        abort(403);
    }
    $stream = LessonStream::findOrFail($streamId);
    $path = storage_path('app/'.$stream->file_path);

    if (!file_exists($path)) abort(404);

    // support range requests - essential for media players
    return response()->stream(function () use ($path) {
        $stream = fopen($path, 'rb');
        while (!feof($stream)) {
            echo fread($stream, 1024 * 8);
            flush();
        }
        fclose($stream);
    }, 200, [
        'Content-Type' => $stream->mime ?? mime_content_type($path),
        'Accept-Ranges' => 'bytes',
        'Content-Length' => filesize($path),
        'Content-Disposition' => 'inline; filename="'.basename($path).'"'
    ]);
})->name('local.lesson.stream');


Route::get('chapters/{chapter}/lessons', [LessonController::class,'index']);
Route::post('chapters/{chapter}/lessons', [LessonController::class,'store']);
Route::get('lessons/{lesson}', [LessonController::class,'show']);
Route::put('lessons/{lesson}', [LessonController::class,'update']);
Route::delete('lessons/{lesson}', [LessonController::class,'destroy']);

// stream endpoint (protected) - returns signed URL
Route::get('lessons/{lesson}/stream', [LessonController::class,'stream']);


Route::prefix('v1')->group(function () {
    Route::apiResource('chapters.lessons', LessonController::class);
    Route::get('local/lesson-stream/{streamId}', [\App\Http\Controllers\LocalStreamController::class,'stream'])
         ->name('local.lesson.stream');
});
