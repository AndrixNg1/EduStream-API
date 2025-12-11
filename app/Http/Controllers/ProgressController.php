<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Course;
use App\Services\Progress\ProgressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    public function __construct(private ProgressService $service) {}

    public function update(Request $request, Lesson $lesson): JsonResponse
    {
        $action = $request->query('action', 'in_progress');

        if ($action === 'complete') {
            $progress = $this->service->markComplete(auth()->user(), $lesson);
        } else {
            $progress = $this->service->markInProgress(auth()->user(), $lesson);
        }

        return response()->json(['message' => 'Progress updated', 'data' => $progress]);
    }

    public function courseProgress(Course $course): JsonResponse
    {
        $percent = $this->service->courseProgress(auth()->user(), $course->id);
        return response()->json(['course_id' => $course->id, 'progress' => $percent]);
    }
}
