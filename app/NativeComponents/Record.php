<?php

namespace App\NativeComponents;

use App\NativeComponents\Concerns\HasTeleprompter;
use Illuminate\View\View;
use Madfortech\CameraOverlay\Facades\CameraOverlay;
use Native\Mobile\Attributes\On;
use Native\Mobile\Edge\Element;
use Native\Mobile\Edge\NativeComponent;
use Native\Mobile\Events\Camera\VideoCancelled;
use Native\Mobile\Events\Camera\VideoRecorded;
use Throwable;

class Record extends NativeComponent
{
    use HasTeleprompter;

    public string $recordingStatus = 'Ready to record';

    public ?string $pendingVideoPath = null;

    public ?string $pendingVideoMimeType = null;

    public function startRecording(): void
    {
        if (trim($this->script) === '') {
            $this->recordingStatus = 'Please write a script first';

            return;
        }

        $this->discardPendingFile();

        try {
            $started = CameraOverlay::recordVideo()
                ->script($this->script)
                ->maxDuration(60)
                ->countdown(3)
                ->id('telescript-recording')
                ->start();

            if (! $started) {
                $this->scrolling = false;
                $this->recordingStatus = 'Unable to start camera. Please rebuild the app.';

                return;
            }

            $this->position = 0;
            $this->scrolling = false;
            $this->recordingStatus = 'Camera is opening...';
        } catch (Throwable $e) {
            report($e);

            $this->scrolling = false;
            $this->recordingStatus = 'Unable to start recording';
        }
    }

    #[On(VideoRecorded::class)]
    public function handleVideoRecorded(
        string $path,
        string $mimeType,
        ?string $id = null
    ): void {
        if ($id !== 'telescript-recording') {
            return;
        }

        $this->scrolling = false;

        if (! is_file($path)) {
            $this->recordingStatus = 'Recording file not found';

            return;
        }

        $this->discardPendingFile();
        $this->pendingVideoPath = $path;
        $this->pendingVideoMimeType = $mimeType;
        $this->recordingStatus = 'Recording complete. Save video?';
    }

    public function savePendingVideo(): void
    {
        if (! $this->pendingVideoPath || ! is_file($this->pendingVideoPath)) {
            $this->clearPendingVideo();
            $this->recordingStatus = 'Recording file not found';

            return;
        }

        $path = $this->pendingVideoPath;

        try {
            $savedToGallery = CameraOverlay::saveToGallery($path);

            @unlink($path);
            $this->resetRecordingData();
            $this->recordingStatus = $savedToGallery
                ? 'Video saved to gallery. App data reset.'
                : 'Could not save to gallery. Video discarded and app data reset.';
        } catch (Throwable $e) {
            report($e);

            @unlink($path);
            $this->resetRecordingData();
            $this->recordingStatus = 'Could not save to gallery. Video discarded and app data reset.';
        }
    }

    public function discardPendingVideo(): void
    {
        $this->discardPendingFile();
        $this->clearPendingVideo();
        $this->recordingStatus = 'Video discarded';
    }

    #[On(VideoCancelled::class)]
    public function handleVideoCancelled(bool $cancelled = true, ?string $id = null): void
    {
        if ($id !== null && $id !== 'telescript-recording') {
            return;
        }

        $this->scrolling = false;
        $this->discardPendingFile();
        $this->clearPendingVideo();
        $this->recordingStatus = 'Recording cancelled';
    }

    private function discardPendingFile(): void
    {
        if ($this->pendingVideoPath && is_file($this->pendingVideoPath)) {
            @unlink($this->pendingVideoPath);
        }
    }

    private function clearPendingVideo(): void
    {
        $this->pendingVideoPath = null;
        $this->pendingVideoMimeType = null;
    }

    private function resetRecordingData(): void
    {
        $this->script = '';
        $this->position = 0;
        $this->scrolling = false;
        $this->clearPendingVideo();
    }

    public function render(): Element|View
    {
        return view('native.record');
    }
}
