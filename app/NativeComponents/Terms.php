<?php

namespace App\NativeComponents;

use Illuminate\View\View;
use Native\Mobile\Edge\Element;
use Native\Mobile\Edge\NativeComponent;

class Terms extends NativeComponent
{
    public function render(): Element|View
    {
        return view('native.terms');
    }
}
