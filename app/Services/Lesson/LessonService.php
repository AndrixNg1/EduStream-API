<?php

namespace App\Services\Lesson;

use App\Jobs\ProcessLessonMediaJob;
use App\Models\Lesson;
use App\Repositories\LessonRepository;

class LessonService
{
    public function __construct(
        private LessonRepository $repo,
        private LessonUploadService $uploadService
    ){}

    public function listForChapter(int $chapterId)
    {
        return $this->repo->getByChapter($chapterId);
    }

    public function createWithUpload(array $data, $file, int $userId): Lesson
    {
        // data includes: title, description, type, position, is_free...
        $lesson = $this->repo->create(array_merge($data, ['position'=>$data['position'] ?? 1]));

        // store original file
        $fileMeta = $this->uploadService->storeUploadedFile($file, $lesson->id, $lesson->type);

        $lesson->original_path = $fileMeta['path'];
        $lesson->save();

        // dispatch processing job depending on type
        ProcessLessonMediaJob::dispatch($lesson);

        return $lesson->refresh();
    }

    public function update(Lesson $lesson, array $data)
    {
        return $this->repo->update($lesson, $data);
    }

    public function delete(Lesson $lesson)
    {
        // delete streams files too maybe
        foreach($lesson->streams as $s) {
            $this->uploadService->deletePath($s->file_path);
        }
        if ($lesson->original_path) {
            $this->uploadService->deletePath($lesson->original_path);
        }
        $this->repo->delete($lesson);
    }
}
