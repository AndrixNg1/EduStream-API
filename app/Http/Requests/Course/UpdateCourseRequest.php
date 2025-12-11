<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourseRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'min:3'],
            'description' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'string'],
            'level' => ['sometimes', 'in:beginner,intermediate,advanced'],
            'is_published' => ['sometimes', 'boolean'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
