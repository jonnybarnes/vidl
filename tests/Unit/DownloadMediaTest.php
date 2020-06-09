<?php

use App\Commands\YoutubeDL;
use App\Events\MediaDownloaded;
use App\Jobs\DownloadMedia;
use App\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

uses(RefreshDatabase::class);

test('download media job triggers event on success', function () {
    // Setup Media and Job
    $media = new Media();
    $media->save();
    $downloadMediaJob = new DownloadMedia($media->id, 'https://www.youtube.com/watch?v=BaW_jenozKc', 1080);

    Event::fake();

    // Create mock YoutubeDL class
    $this->mock(YoutubeDL::class, function ($mock) use ($media) {
        $mock->shouldReceive('downloadMediaUrl')->once()->andReturn(
            storage_path('/media/' . $media->id . '/youtube-dl_test_video_a.mp4')
        );
    });

    $downloadMediaJob->handle(resolve(YoutubeDL::class));

    Event::assertDispatched(MediaDownloaded::class);
});
