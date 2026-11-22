<?php

namespace App\NativeComponents;

use Illuminate\View\View;
use Native\Mobile\Edge\Element;
use Native\Mobile\Edge\NativeComponent;

class More extends NativeComponent
{
    public function openAbout(): void
    {
        $this->navigate('/about');
    }

    public function openPrivacy(): void
    {
        $this->navigate('/privacy');
    }

    public function openTerms(): void
    {
        $this->navigate('/terms');
    }

    public function render(): Element|View
    {
        return view('native.more');
    }
}
