<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\GoogleCalendarController;
use Illuminate\Support\Facades\Route;
use Spatie\GoogleCalendar\Event;
use Laravel\Socialite\Facades\Socialite;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/tasks/public/{token}', [TaskController::class, 'showPublic'])->name('tasks.public.show');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('tasks', TaskController::class);
    Route::post('/tasks/{task}/generate-public-link', [TaskController::class, 'generatePublicLink'])->name('tasks.generatePublicLink');
    Route::get('/google/redirect', [GoogleCalendarController::class, 'redirect'])->name('google.redirect');
    Route::get('/google/callback', [GoogleCalendarController::class, 'callback'])->name('google.callback');
    Route::post('/tasks/{task}/add-to-google-calendar', [TaskController::class, 'addToGoogleCalendar'])->name('tasks.addToGoogleCalendar');
});

require __DIR__.'/auth.php';
