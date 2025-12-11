<?php

namespace App\Repositories;

use App\Models\Lesson;
use Illuminate\Database\Eloquent\Collection;

class LessonRepository
{
    public function getByChapter(int $chapterId): Collection
    {
        return Lesson::where('chapter_id',$chapterId)->with('streams')->orderBy('position')->get();
    }

    public function find(int $id): Lesson
    {
        return Lesson::with('streams')->findOrFail($id);
    }

    public function create(array $data): Lesson
    {
        return Lesson::create($data);
    }

    public function update(Lesson $lesson, array $data): Lesson
    {
        $lesson->update($data);
        return $lesson;
    }

    public function delete(Lesson $lesson): void
    {
        $lesson->delete();
    }
}
