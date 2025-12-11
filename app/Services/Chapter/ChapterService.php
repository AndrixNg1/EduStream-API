<?php

namespace App\Services\Chapter;

use App\Models\Chapter;
use App\Repositories\ChapterRepository;
use Illuminate\Support\Str;

class ChapterService
{
    public function __construct(private ChapterRepository $repo) {}

    public function listForCourse(int $courseId)
    {
        return $this->repo->getByCourse($courseId);
    }

    public function createForCourse(int $courseId, array $data, int $userId): Chapter
    {
        $data['course_id'] = $courseId;
        $data['created_by'] = $userId;

        // generate unique slug within course
        if (empty($data['slug']) && ! empty($data['title'])) {
            $base = Str::slug($data['title']);
            $slug = $base;
            $i = 1;
            while (Chapter::where('course_id', $courseId)->where('slug', $slug)->exists()) {
                $slug = $base . '-' . $i++;
            }
            $data['slug'] = $slug;
        }

        // if no position provided, Chapter model will assign next position
        return $this->repo->create($data);
    }

    public function update(Chapter $chapter, array $data): Chapter
    {
        // if title changes, optionally update slug (policy decision)
        if (isset($data['title']) && empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        // handle position changes (simple approach: overwrite)
        return $this->repo->update($chapter, $data);
    }

    public function delete(Chapter $chapter): void
    {
        $this->repo->delete($chapter);
    }
}
