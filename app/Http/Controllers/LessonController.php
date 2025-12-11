<?php

namespace App\Http\Controllers;

use App\Http\Requests\Lesson\StoreLessonRequest;
use App\Http\Requests\Lesson\UpdateLessonRequest;
use App\Models\Chapter;
use App\Models\Lesson;
use App\Repositories\LessonRepository;
use App\Services\Lesson\LessonService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function __construct(
        private LessonService $service,
        private LessonRepository $repo
    ){}

    // list lessons for chapter
    public function index(Chapter $chapter): JsonResponse
    {
        return response()->json($this->service->listForChapter($chapter->id));
    }

    // show lesson
    public function show(Lesson $lesson): JsonResponse
    {
        return response()->json($lesson->load('streams'));
    }

    // upload & create (multipart/form-data)
    public function store(StoreLessonRequest $request, Chapter $chapter): JsonResponse
    {
        $data = $request->validated();
        $data['chapter_id'] = $chapter->id;
        $data['type'] = $data['type'] ?? 'video';

        $file = $request->file('file');

        $lesson = $this->service->createWithUpload($data, $file, auth()->id());

        return response()->json([
            'message' => 'Lesson created and processing started',
            'data' => $lesson
        ], 201);
    }

    public function update(UpdateLessonRequest $request, Lesson $lesson): JsonResponse
    {
        $updated = $this->service->update($lesson, $request->validated());
        return response()->json(['message'=>'Lesson updated','data'=>$updated]);
    }

    public function destroy(Lesson $lesson): JsonResponse
    {
        $this->service->delete($lesson);
        return response()->json(['message'=>'Lesson deleted']);
    }

    // streaming url endpoint: returns signed url to the requested quality
    public function stream(Request $request, Lesson $lesson)
    {
        $quality = $request->query('quality'); // e.g. 720
        $stream = null;

        if ($quality) {
            $stream = $lesson->streams()->where('quality', $quality.'p')->latest()->first();
        }

        // fallback to master or original
        if (!$stream) {
            $stream = $lesson->streams()->latest()->first();
        }

        if (!$stream) {
            return response()->json(['message'=>'No stream available'], 404);
        }

        // Signed url generation
        $disk = config('filesystems.default'); // if s3 use temporaryUrl
        if (config('filesystems.default') === 's3') {
            $url = \Storage::disk('s3')->temporaryUrl($stream->file_path, now()->addSeconds(60));
        } else {
            // local signed route - create route 'lessons.stream.local'
            $url = url(\Illuminate\Support\Facades\URL::signedRoute('local.lesson.stream', ['streamId'=>$stream->id], now()->addSeconds(60)));
        }

        return response()->json([
            'url' => $url,
            'expires_in' => 60
        ]);
    }
}
