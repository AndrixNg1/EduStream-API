<?php

namespace App\Repositories;

use App\Models\Chapter;
use Illuminate\Database\Eloquent\Collection;

class ChapterRepository
{
    public function getByCourse(int $courseId): Collection
    {
        return Chapter::where('course_id', $courseId)
                      ->orderBy('position')
                      ->get();
    }

    public function find(int $id): Chapter
    {
        return Chapter::findOrFail($id);
    }

    public function create(array $data): Chapter
    {
        return Chapter::create($data);
    }

    public function update(Chapter $chapter, array $data): Chapter
    {
        $chapter->update($data);
        return $chapter;
    }

    public function delete(Chapter $chapter): void
    {
        $chapter->delete();
    }
}
