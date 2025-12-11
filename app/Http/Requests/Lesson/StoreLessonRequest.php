<?php

namespace App\Http\Requests\Lesson;

use Illuminate\Foundation\Http\FormRequest;

class StoreLessonRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'title' => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'file' => ['required','file','max:512000'], // 500MB adjust as needed
            'type' => ['required','in:video,audio,pdf,other'],
            'position' => ['sometimes','integer','min:1'],
            'is_free' => ['sometimes','boolean'],
        ];
    }
}
