<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3'],
            'description' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'string'],
            'level' => ['required', 'in:beginner,intermediate,advanced'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
