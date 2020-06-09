<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Commands\YoutubeDL;
use App\Events\MediaDownloaded;
use App\Media;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DownloadMedia implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $mediaId;
    protected string $mediaUrl;
    protected int $quality;

    /**
     * Create a new job instance.
     *
     * @param string $mediaId
     * @param string $mediaUrl
     * @param int $quality
     */
    public function __construct(string $mediaId, string $mediaUrl, int $quality)
    {
        $this->mediaId = $mediaId;
        $this->mediaUrl = $mediaUrl;
        $this->quality = $quality;
    }

    /**
     * Execute the job.
     *
     * @param YoutubeDL $youtubeDL
     * @return void
     */
    public function handle(YoutubeDL $youtubeDL): void
    {
        $fileLocation = $youtubeDL->downloadMediaUrl($this->mediaUrl, $this->mediaId, $this->quality);

        $media = Media::findOrFail($this->mediaId);
        $media->file_location = $fileLocation;
        $media->save();

        event(new MediaDownloaded($media));
    }
}
