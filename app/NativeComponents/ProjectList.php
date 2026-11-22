<?php

namespace App\NativeComponents;

use App\NativeComponents\Concerns\HasTeleprompter;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Native\Mobile\Attributes\On;
use Native\Mobile\Edge\Element;
use Native\Mobile\Edge\NativeComponent;
use Native\Mobile\Events\Microphone\MicrophoneCancelled;
use Native\Mobile\Events\Microphone\MicrophoneRecorded;
use Native\Mobile\Facades\Microphone;
use Throwable;

class ProjectList extends NativeComponent
{
    use HasTeleprompter;

    public bool $recording = false;

    public string $status = 'Ready to record';

    public function startAudio(): void
    {
        if (trim($this->script) === '') {
            $this->status = 'Please write a script first';

            return;
        }

        try {
            Microphone::record()->id('telescript-audio')->start();

            $this->position = 0;
            $this->scrolling = true;
            $this->recording = true;
            $this->status = 'Recording...';
        } catch (Throwable $e) {
            report($e);
            $this->status = 'Unable to start recording';
        }
    }

    public function stopAudio(): void
    {
        try {
            Microphone::stop();
        } catch (Throwable $e) {
            report($e);
        }

        $this->scrolling = false;
        $this->recording = false;
        $this->status = 'Saving...';
    }

    #[On(MicrophoneRecorded::class)]
    public function handleAudioRecorded(string $path, string $mimeType, ?string $id = null): void
    {
        if ($id !== 'telescript-audio') {
            return;
        }

        $this->recording = false;
        $this->scrolling = false;

        if (! is_file($path)) {
            $this->status = 'Recording file not found';

            return;
        }

        $stream = null;

        try {
            $stream = fopen($path, 'rb');

            if ($stream === false) {
                $this->status = 'Unable to read audio file';

                return;
            }

            $ext = str_contains($mimeType, 'wav') ? 'wav' : 'm4a';
            $filename = 'audios/'.bin2hex(random_bytes(16)).'.'.$ext;

            if (! Storage::disk('local')->writeStream($filename, $stream)) {
                $this->status = 'Unable to save audio';

                return;
            }

            @unlink($path);
            $this->status = 'Audio saved successfully';
        } catch (Throwable $e) {
            report($e);
            $this->status = 'Something went wrong while saving audio';
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
        }
    }

    #[On(MicrophoneCancelled::class)]
    public function handleAudioCancelled(bool $cancelled = true, ?string $id = null, string $reason = ''): void
    {
        $this->recording = false;
        $this->scrolling = false;
        $this->status = $reason === 'permission_denied'
            ? 'Please allow microphone access in Settings'
            : 'Recording cancelled';
    }

    public function render(): Element|View
    {
        return view('native.project-list');
    }
}
