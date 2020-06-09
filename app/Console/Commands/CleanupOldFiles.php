<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Media;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Carbon;

class CleanupOldFiles
{
    public function __invoke(): void
    {
        $fs = new Filesystem();
        $oldMedia = Media::where('created_at', '<', Carbon::now()->subDay());

        $oldMedia->each(function (Media $media) use ($fs) {
            if ($media->file_location) {
                $fs->delete($media->file_location);
                $fs->deleteDirectory(dirname($media->file_location));
            }
            $media->delete();
        });
    }
}
