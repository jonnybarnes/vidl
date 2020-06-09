<?php

use App\Jobs\DownloadMedia;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

test('we can create a new media instance', function () {
    Queue::fake();

    $response = $this->postJson('/api/media', [
        'url' => 'https://www.youtube.com/watch?v=BaW_jenozKc',
        'quality' => 1080,
    ]);
    $response
        ->assertStatus(201)
        ->assertJson(['success' => true]);

    Queue::assertPushed(DownloadMedia::class);
});

test('we can download a saved file', function () {
    // Setup a saved file
    $uuid = Str::uuid();
    $fileSystem = new FileSystem();
    $fileSystem->makeDirectory(storage_path('/media/' . $uuid));
    $fileSystem->copy(__DIR__ . '/../test.mp4', storage_path('/media/' . $uuid . '/test.mp4'));
    $fileLocation = storage_path('/media/' . $uuid . '/test.mp4');
    DB::table('media')->insert([
        'id' => $uuid,
        'file_location' => $fileLocation,
        'created_at' => Carbon::now()->toDateTimeString(),
        'updated_at' => Carbon::now()->toDateTimeString(),
    ]);

    // Assert file download
    $response = $this->get('/media/download/' . $uuid);
    $response->assertHeader('content-disposition', 'attachment; filename=test.mp4');

    // Delete the file
    $fileSystem->delete(storage_path('/media/' . $uuid . '/test.mp4'));
    $fileSystem->deleteDirectory(storage_path('/media/' . $uuid));
});
