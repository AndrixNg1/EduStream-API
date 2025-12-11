<?php

namespace App\Repositories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Collection;

class CourseRepository
{
    public function all(): Collection
    {
        return Course::with('user')->latest()->get();
    }

    public function find(int $id): Course
    {
        return Course::with('chapters')->findOrFail($id);
    }

    public function create(array $data): Course
    {
        return Course::create($data);
    }

    public function update(Course $course, array $data): Course
    {
        $course->update($data);
        return $course;
    }

    public function delete(Course $course): void
    {
        $course->delete();
    }
}
