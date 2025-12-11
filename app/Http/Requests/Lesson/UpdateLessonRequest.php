<?php

namespace App\Http\Requests\Lesson;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLessonRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'title' => ['sometimes','string','max:255'],
            'description' => ['nullable','string'],
            'position' => ['sometimes','integer','min:1'],
            'is_free' => ['sometimes','boolean'],
            'is_published' => ['sometimes','boolean'],
        ];
    }
}
