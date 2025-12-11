<?php

namespace App\Http\Controllers;

use App\Models\LessonStream;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LocalStreamController extends Controller
{
    /**
     * Serve a local file with proper support for HTTP Range (206 partial).
     * The route must be signed (URL::signedRoute) so we only need to check signature.
     *
     * @param Request $request
     * @param int $streamId
     * @return StreamedResponse|\Illuminate\Http\Response
     */
    public function stream(Request $request, int $streamId)
    {
        if (! $request->hasValidSignature()) {
            return response()->json(['message'=>'Unauthorized'], 403);
        }

        $stream = LessonStream::findOrFail($streamId);
        $filePath = storage_path('app/' . $stream->file_path);

        if (!is_file($filePath) || !is_readable($filePath)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        $size = filesize($filePath);
        $mime = $stream->mime ?: mime_content_type($filePath);
        $headers = [
            'Content-Type' => $mime,
            'Accept-Ranges' => 'bytes',
            'Content-Disposition' => 'inline; filename="'.basename($filePath).'"'
        ];

        $start = 0;
        $length = $size;
        $status = 200;

        // handle Range header
        if ($request->headers->has('Range')) {
            $range = $request->header('Range');
            if (preg_match('/bytes=(\d*)-(\d*)/', $range, $matches)) {
                $start = $matches[1] !== '' ? intval($matches[1]) : 0;
                $end = $matches[2] !== '' ? intval($matches[2]) : ($size - 1);
                if ($end > $size - 1) $end = $size - 1;
                $length = $end - $start + 1;
                $status = 206;
                $headers['Content-Range'] = "bytes {$start}-{$end}/{$size}";
                $headers['Content-Length'] = $length;
            }
        } else {
            $headers['Content-Length'] = $size;
        }

        $response = new StreamedResponse(function () use ($filePath, $start, $length) {
            $chunkSize = 1024 * 1024; // 1MB
            $handle = fopen($filePath, 'rb');
            if ($start) {
                fseek($handle, $start);
            }
            $bytesRemaining = $length;
            while ($bytesRemaining > 0 && !feof($handle)) {
                $read = min($chunkSize, $bytesRemaining);
                $buffer = fread($handle, $read);
                echo $buffer;
                flush();
                $bytesRemaining -= strlen($buffer);
            }
            fclose($handle);
        }, $status, $headers);

        return $response;
    }
}
