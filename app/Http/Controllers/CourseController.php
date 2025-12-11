<?php

namespace App\Http\Controllers;

use App\Http\Requests\Course\StoreCourseRequest;
use App\Http\Requests\Course\UpdateCourseRequest;
use App\Models\Course;
use App\Services\Course\CourseService;

class CourseController extends Controller
{
    public function __construct(private CourseService $service) {}

    public function index()
    {
        return response()->json($this->service->list());
    }

    public function store(StoreCourseRequest $request)
    {
        $course = $this->service->create($request->validated(), auth()->id());

        return response()->json([
            'message' => 'Course created successfully',
            'data' => $course
        ], 201);
    }

    public function show(Course $course)
    {
        return response()->json($course->load('chapters'));
    }

    public function update(UpdateCourseRequest $request, Course $course)
    {
        $updated = $this->service->update($course, $request->validated());

        return response()->json([
            'message' => 'Course updated successfully',
            'data' => $updated
        ]);
    }

    public function destroy(Course $course)
    {
        $this->service->delete($course);

        return response()->json(['message' => 'Course deleted successfully']);
    }
}
