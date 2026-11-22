<?php

use App\NativeComponents\Record;
use Native\Mobile\Testing\Native;

it('starts the camera with a countdown before recording', function () {
    Native::fakeBridge()->respondTo('CameraOverlay.RecordVideo', ['status' => 'success']);

    Native::test(Record::class)
        ->set('script', 'Hello world')
        ->call('startRecording');

    Native::fakeBridge()->assertCalled('CameraOverlay.RecordVideo', fn (array $params) => $params['script'] === 'Hello world'
        && $params['countdown'] === 3
        && $params['maxDuration'] === 60);
});

it('saves only to the gallery and resets app data', function () {
    Native::fakeBridge()->respondTo('CameraOverlay.SaveToGallery', ['status' => 'success']);
    $path = tempnam(sys_get_temp_dir(), 'telescript_');
    file_put_contents($path, 'video bytes');

    Native::test(Record::class)
        ->set('script', 'Hello world')
        ->call('handleVideoRecorded', $path, 'video/mp4', 'telescript-recording')
        ->assertSee('Video saves to gallery only. No in-app copy is kept; app data resets after saving.')
        ->tap('Save to Gallery')
        ->assertSee('Video saved to gallery. App data reset.')
        ->assertSet('script', '')
        ->assertSet('pendingVideoPath', null);

    Native::fakeBridge()->assertCalled('CameraOverlay.SaveToGallery', fn (array $params) => $params['path'] === $path);

    expect(file_exists($path))->toBeFalse();
});

it('discards the video and resets data when the gallery save fails', function () {
    Native::fakeBridge()->respondTo('CameraOverlay.SaveToGallery', ['status' => 'error', 'message' => 'denied']);
    $path = tempnam(sys_get_temp_dir(), 'telescript_');
    file_put_contents($path, 'video bytes');

    Native::test(Record::class)
        ->set('script', 'Hello world')
        ->call('handleVideoRecorded', $path, 'video/mp4', 'telescript-recording')
        ->tap('Save to Gallery')
        ->assertSee('Could not save to gallery. Video discarded and app data reset.')
        ->assertSet('script', '');

    expect(file_exists($path))->toBeFalse();
});
