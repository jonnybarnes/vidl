<?php

use App\Console\Commands\CleanupOldFiles;
use App\Media;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

test('old media files are deleted', function () {
    // Setup
    $uuid = Str::uuid();
    $fileSystem = new Filesystem();
    $fileSystem->makeDirectory(storage_path('/media/' . $uuid));
    $fileSystem->copy(__DIR__ . '/../test.mp4', storage_path('/media/' . $uuid . '/test.mp4'));
    $fileLocation = storage_path('/media/' . $uuid . '/test.mp4');
    DB::table('media')->insert([
        'id' => $uuid,
        'file_location' => $fileLocation,
        'created_at' => Carbon::now()->subDays(2)->toDateTimeString(),
        'updated_at' => Carbon::now()->subDays(2)->toDateTimeString(),
    ]);
    $newMedia = new Media();
    $newMedia->save();

    // Assert initial conditions
    $this->assertDatabaseHas('media', ['id' => $uuid]);
    assertCount(2, DB::table('media')->get());
    assertFileExists(storage_path('/media/' . $uuid . '/test.mp4'));

    // Act
    $cleanupOldFiles = new CleanupOldFiles();
    $cleanupOldFiles();

    // Assert final conditions
    $this->assertDatabaseMissing('media', ['id' => $uuid]);
    assertCount(1, DB::table('media')->get());
    assertFileDoesNotExist(storage_path('/media/' . $uuid . '/test.mp4'));
});
