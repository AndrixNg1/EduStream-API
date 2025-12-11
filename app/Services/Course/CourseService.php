<?php

namespace App\Services\Course;

use App\Models\Course;
use App\Repositories\CourseRepository;

class CourseService
{
    public function __construct(
        private CourseRepository $repo
    ) {}

    public function list()
    {
        return $this->repo->all();
    }

    public function create(array $data, int $userId): Course
    {
        $data['user_id'] = $userId;
        return $this->repo->create($data);
    }

    public function update(Course $course, array $data): Course
    {
        return $this->repo->update($course, $data);
    }

    public function delete(Course $course): void
    {
        $this->repo->delete($course);
    }
}
