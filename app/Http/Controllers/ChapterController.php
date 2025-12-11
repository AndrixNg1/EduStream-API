<?php

namespace App\Http\Controllers;

use App\Http\Requests\Chapter\StoreChapterRequest;
use App\Http\Requests\Chapter\UpdateChapterRequest;
use App\Models\Chapter;
use App\Models\Course;
use App\Repositories\ChapterRepository;
use App\Services\Chapter\ChapterService;
use Illuminate\Http\JsonResponse;

class ChapterController extends Controller
{
    public function __construct(
        private ChapterService $service,
        private ChapterRepository $repo
    ) {}

    // GET /api/v1/courses/{course}/chapters
    public function index(Course $course): JsonResponse
    {
        $chapters = $this->service->listForCourse($course->id);
        return response()->json($chapters);
    }

    // GET /api/v1/chapters/{chapter}
    public function show(Chapter $chapter): JsonResponse
    {
        return response()->json($chapter->load('lessons'));
    }

    // POST /api/v1/courses/{course}/chapters
    public function store(StoreChapterRequest $request, Course $course): JsonResponse
    {
        $data = $request->validated();
        $chapter = $this->service->createForCourse($course->id, $data, auth()->id());

        return response()->json([
            'message' => 'Chapter created',
            'data' => $chapter
        ], 201);
    }

    // PUT /api/v1/chapters/{chapter}
    public function update(UpdateChapterRequest $request, Chapter $chapter): JsonResponse
    {
        $updated = $this->service->update($chapter, $request->validated());

        return response()->json([
            'message' => 'Chapter updated',
            'data' => $updated
        ]);
    }

    // DELETE /api/v1/chapters/{chapter}
    public function destroy(Chapter $chapter): JsonResponse
    {
        $this->service->delete($chapter);

        return response()->json(['message' => 'Chapter deleted']);
    }
}
