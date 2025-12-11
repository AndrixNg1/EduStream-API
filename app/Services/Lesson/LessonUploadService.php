<?php

namespace App\Services\Lesson;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LessonUploadService
{
    protected string $disk = 'local'; // use local private storage; change to s3 in production

    public function storeUploadedFile(UploadedFile $file, int $lessonId, string $subfolder = ''): array
    {
        $ext = $file->getClientOriginalExtension();
        $folder = "private/lessons/{$lessonId}" . ($subfolder ? "/{$subfolder}" : '');
        $filename = Str::random(16) . '.' . $ext;
        $relative = "{$folder}/{$filename}";

        // store in storage/app/...
        $path = $file->storeAs($folder, $filename, $this->disk);

        return [
            'path' => $path,
            'mime' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'filename' => $filename,
        ];
    }

    public function deletePath(string $relative): bool
    {
        return Storage::disk($this->disk)->delete($relative);
    }
}
