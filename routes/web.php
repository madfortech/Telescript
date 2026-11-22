<?php

use App\Http\Controllers\ProfileController;
use App\NativeComponents\About;
use App\NativeComponents\Layouts\StackLayout;
use App\NativeComponents\More;
use App\NativeComponents\Privacy;
use App\NativeComponents\ProjectList;
use App\NativeComponents\Record;
use App\NativeComponents\Terms;
use Illuminate\Support\Facades\Route;

// ✅ Native Mobile route —
Route::native('/', ProjectList::class)

    ->layout(StackLayout::class);

Route::native('/more', More::class)
    ->layout(StackLayout::class);

Route::native('/about', About::class)
    ->layout(StackLayout::class);

Route::native('/privacy', Privacy::class)
    ->layout(StackLayout::class);

Route::native('/terms', Terms::class)
    ->layout(StackLayout::class);

Route::native('/record', Record::class)
    ->layout(StackLayout::class);

// Web routes (browser fallback ke liye, mobile pe nahi khulenge)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
