<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Commands\YoutubeDL;
use App\Jobs\DownloadMedia;
use App\Media;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Process\Process;

class MediaController extends Controller
{
    public function create(YoutubeDL $youtubeDL): JsonResponse
    {
        $mediaUrl = filter_var(request('url'), FILTER_VALIDATE_URL);

        if ($mediaUrl === false) {
            return response()->json([
                'success' => false,
            ], 400);
        }

        $verifiedUrl = $youtubeDL->verifyMediaUrl($mediaUrl);

        if (!$verifiedUrl) {
            return response()->json([
                'success' => false,
            ], 400);
        }

        $media = Media::create();

        DownloadMedia::dispatch(
            $media->id,
            request('url'),
            (int) request('quality')
        );

        return response()->json([
            'success' => true,
            'media_id' => $media->id,
        ], 201);
    }

    public function download(Media $media): BinaryFileResponse
    {
        abort_if(empty($media->file_location), 404);

        return response()->download($media->file_location);
    }
}
