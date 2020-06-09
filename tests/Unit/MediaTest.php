<?php

use App\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// The regex pattern for this test is from https://stackoverflow.com/a/12808694/12854
test('media models have uuid unique ids', function () {
    $media = new Media();
    $media->save();
    $uuid = $media->id;

    assertEquals(1, preg_match(
        '/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i',
        $uuid
    ));
});

test('media download url is auto-generated from id', function () {
    $media = new Media();
    $media->save();
    $uuid = $media->id;

    assertStringContainsString('/media/download/' . $uuid, $media->getDownloadUrl());
});
