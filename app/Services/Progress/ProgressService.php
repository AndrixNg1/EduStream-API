<?php

namespace App\Services\Progress;

use App\Models\Lesson;
use App\Models\User;
use App\Repositories\ProgressRepository;

class ProgressService
{
    public function __construct(private ProgressRepository $repo) {}

    public function markInProgress(User $user, Lesson $lesson)
    {
        return $this->repo->createOrUpdate($user, $lesson, [
            'completed' => false,
            'last_watched_at' => now()
        ]);
    }

    public function markComplete(User $user, Lesson $lesson)
    {
        return $this->repo->createOrUpdate($user, $lesson, [
            'completed' => true,
            'last_watched_at' => now()
        ]);
    }

    public function courseProgress(User $user, int $courseId)
    {
        return $this->repo->getCourseProgress($user, $courseId);
    }
}
