<?php

namespace App\Repositories;

use App\Models\Progress;
use App\Models\Lesson;
use App\Models\User;

class ProgressRepository
{
    public function find(User $user, Lesson $lesson): ?Progress
    {
        return Progress::where('user_id', $user->id)
            ->where('lesson_id', $lesson->id)
            ->first();
    }

    public function createOrUpdate(User $user, Lesson $lesson, array $data): Progress
    {
        return Progress::updateOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lesson->id],
            $data
        );
    }

    public function getCourseProgress(User $user, int $courseId)
    {
        $lessons = Lesson::where('course_id', $courseId)->pluck('id');
        $completed = Progress::where('user_id', $user->id)
            ->whereIn('lesson_id', $lessons)
            ->where('completed', true)
            ->count();

        $total = count($lessons);

        return $total > 0 ? round(($completed / $total) * 100, 2) : 0;
    }
}
