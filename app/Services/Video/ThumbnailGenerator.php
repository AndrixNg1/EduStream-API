<?php

namespace App\Services\Video;

use Illuminate\Support\Str;

class ThumbnailGenerator
{
    protected string $ffmpeg;
    protected string $disk = 'local';

    public function __construct()
    {
        $this->ffmpeg = config('files.ffmpeg_path', env('FFMPEG_PATH', '/usr/bin/ffmpeg'));
    }

    /**
     * Generate thumbnail for a video and store under:
     * storage/app/private/lessons/{lessonId}/thumb.jpg
     *
     * @param string $absoluteInputPath
     * @param int $lessonId
     * @param int $atSeconds
     * @return string relative path (storage/app/...)
     */
    public function generateFromVideo(string $absoluteInputPath, int $lessonId, int $atSeconds = 5): string
    {
        $folderRel = "private/lessons/{$lessonId}";
        $folderAbs = storage_path('app/' . $folderRel);

        if (!is_dir($folderAbs)) {
            mkdir($folderAbs, 0755, true);
        }

        $outRel = "{$folderRel}/thumb.jpg";
        $outAbs = storage_path('app/' . $outRel);

        // prefer seeking first (-ss before -i) for speed
        // use select I-frame as fallback is more complex; common approach: -ss then -frames:v 1
        $cmd = escapeshellcmd($this->ffmpeg)
            . ' -ss ' . escapeshellarg("{$atSeconds}")
            . ' -i ' . escapeshellarg($absoluteInputPath)
            . ' -vframes 1 -q:v 2 ' . escapeshellarg($outAbs);

        exec($cmd . ' 2>&1', $output, $returnVar);

        if ($returnVar !== 0) {
            \Log::warning("Thumbnail generation failed for lesson {$lessonId}", ['cmd'=>$cmd, 'output'=>$output]);
            // fallback: try without -ss (grab first frame)
            $cmd2 = escapeshellcmd($this->ffmpeg) . ' -i ' . escapeshellarg($absoluteInputPath)
                . " -vf \"select='eq(pict_type,I)'\" -vsync vfr -frames:v 1 " . escapeshellarg($outAbs);
            exec($cmd2 . ' 2>&1', $out2, $r2);
            if ($r2 !== 0) {
                \Log::error("Thumbnail fallback also failed for lesson {$lessonId}", ['cmd'=>$cmd2,'output'=>$out2]);
                return $outRel; // may not exist, caller must handle
            }
        }

        return $outRel;
    }
}
