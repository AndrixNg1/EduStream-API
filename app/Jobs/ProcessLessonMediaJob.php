<?php

namespace App\Jobs;

use App\Models\Lesson;
use App\Models\LessonStream;
use App\Services\Video\VideoProcessor;
use App\Services\Video\ThumbnailGenerator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use FFMpeg; // optional if you have wrapper

class ProcessLessonMediaJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Lesson $lesson) {}

    public function handle(VideoProcessor $processor, ThumbnailGenerator $thumbGen)
    {
        // absolute path
        $disk = Storage::disk('local');
        $absolute = storage_path('app/' . $this->lesson->original_path);

        if (!file_exists($absolute)) {
            // log and exit
            return;
        }

        $type = $this->lesson->type;

        if ($type === 'video') {
            // transcode to qualities
            $qualities = ['360','480','720'];
            $streams = $processor->convertToQualities($absolute, $qualities, $this->lesson->id);
            // convertToQualities returns array quality=>relativePath

            // thumbnail
            $thumbnailRel = $thumbGen->generateFromVideo($absolute, $this->lesson->id);
            $this->lesson->thumbnail = $thumbnailRel;
            $this->lesson->duration = $processor->getDuration($absolute);
            $this->lesson->save();

            foreach($streams as $q => $rel) {
                LessonStream::create([
                    'lesson_id' => $this->lesson->id,
                    'quality' => "{$q}p",
                    'file_path' => $rel,
                    'container' => 'mp4',
                    'mime' => 'video/mp4',
                    'filesize' => $disk->size($rel) ?? null,
                ]);
            }
        } elseif ($type === 'audio') {
            // convert audio to standard mp3 128k
            $out = "private/lessons/{$this->lesson->id}/audios/".uniqid().".mp3";
            $cmd = escapeshellcmd(config('files.ffmpeg_path', env('FFMPEG_PATH','/usr/bin/ffmpeg')))
                . " -y -i ".escapeshellarg($absolute)
                . " -vn -c:a libmp3lame -b:a 128k ".escapeshellarg(storage_path('app/'.$out));
            exec($cmd, $o, $r);
            if ($r === 0) {
                LessonStream::create([
                    'lesson_id'=>$this->lesson->id,
                    'quality'=>'128k',
                    'file_path'=>$out,
                    'container'=>'mp3',
                    'mime'=>'audio/mpeg',
                    'filesize'=> $disk->size($out) ?? null,
                ]);
            }
            // duration
            $this->lesson->duration = $processor->getDuration($absolute);
            $this->lesson->save();
        } elseif ($type === 'pdf') {
            // optionally generate preview image (first page)
            $preview = "private/lessons/{$this->lesson->id}/pdf/preview_".uniqid().".jpg";
            $convert = config('files.pdf_preview_path', env('PDF_PREVIEW_CMD','/usr/bin/convert')); // ImageMagick convert
            $cmd = "{$convert} ".escapeshellarg($absolute."[0]")." -thumbnail x300 ".escapeshellarg(storage_path('app/'.$preview));
            exec($cmd, $o,$r);
            if ($r === 0) {
                LessonStream::create([
                    'lesson_id'=>$this->lesson->id,
                    'quality'=>'preview',
                    'file_path'=>$preview,
                    'container'=>'jpg',
                    'mime'=>'image/jpeg',
                    'filesize'=> $disk->size($preview) ?? null,
                ]);
                $this->lesson->thumbnail = $preview;
                $this->lesson->save();
            }
        } else {
            // other types - noop or store metadata
        }
    }
}
