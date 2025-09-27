<?php

use App\Http\Controllers\CameraSourceController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StreamController;
use App\Http\Controllers\StreamParticipantController;
use App\Http\Controllers\StreamScheduleController;
use App\Http\Controllers\VideoUploadController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Streams
    Route::get('/streams', [StreamController::class, 'index'])->name('streams.index');
    Route::get('/streams/create', [StreamController::class, 'create'])->name('streams.create');
    Route::post('/streams', [StreamController::class, 'store'])->name('streams.store');
    Route::get('/streams/{stream:uuid}', [StreamController::class, 'show'])->name('streams.show');
    Route::get('/streams/{stream:uuid}/edit', [StreamController::class, 'edit'])->name('streams.edit');
    Route::put('/streams/{stream:uuid}', [StreamController::class, 'update'])->name('streams.update');
    Route::delete('/streams/{stream:uuid}', [StreamController::class, 'destroy'])->name('streams.destroy');

    // Stream Control Actions
    Route::post('/streams/{stream:uuid}/start', [StreamController::class, 'start'])->name('streams.start');
    Route::post('/streams/{stream:uuid}/stop', [StreamController::class, 'stop'])->name('streams.stop');
    Route::post('/streams/{stream:uuid}/join', [StreamController::class, 'join'])->name('streams.join');
    Route::post('/streams/{stream:uuid}/leave', [StreamController::class, 'leave'])->name('streams.leave');

    // Stream Studio (Live streaming interface)
    Route::get('/streams/{stream:uuid}/studio', [StreamController::class, 'studio'])->name('streams.studio');

    // Stream Participants
    Route::post('/streams/{stream:uuid}/participants/invite', [StreamParticipantController::class, 'invite'])->name('participants.invite');
    Route::delete('/participants/{participant}/kick', [StreamParticipantController::class, 'kick'])->name('participants.kick');
    Route::put('/participants/{participant}/settings', [StreamParticipantController::class, 'updateSettings'])->name('participants.settings');

    // Camera Sources
    Route::post('/streams/{stream:uuid}/cameras', [CameraSourceController::class, 'store'])->name('cameras.store');
    Route::post('/cameras/{source:uuid}/switch', [CameraSourceController::class, 'switch'])->name('cameras.switch');
    Route::delete('/cameras/{source:uuid}', [CameraSourceController::class, 'destroy'])->name('cameras.destroy');
    Route::put('/cameras/{source:uuid}/settings', [CameraSourceController::class, 'updateSettings'])->name('cameras.settings');

    // Video Uploads
    Route::get('/videos', [VideoUploadController::class, 'index'])->name('videos.index');
    Route::get('/videos/create', [VideoUploadController::class, 'create'])->name('videos.create');
    Route::post('/videos', [VideoUploadController::class, 'store'])->name('videos.store');
    Route::get('/videos/{upload:uuid}', [VideoUploadController::class, 'show'])->name('videos.show');
    Route::get('/videos/{upload:uuid}/edit', [VideoUploadController::class, 'edit'])->name('videos.edit');
    Route::put('/videos/{upload:uuid}', [VideoUploadController::class, 'update'])->name('videos.update');
    Route::delete('/videos/{upload:uuid}', [VideoUploadController::class, 'destroy'])->name('videos.destroy');
    Route::get('/videos/{upload:uuid}/play', [VideoUploadController::class, 'play'])->name('videos.play');

    // Stream Schedules
    Route::get('/schedules', [StreamScheduleController::class, 'index'])->name('schedules.index');
    Route::get('/schedules/create', [StreamScheduleController::class, 'create'])->name('schedules.create');
    Route::post('/schedules', [StreamScheduleController::class, 'store'])->name('schedules.store');
    Route::get('/schedules/{schedule:uuid}', [StreamScheduleController::class, 'show'])->name('schedules.show');
    Route::get('/schedules/{schedule:uuid}/edit', [StreamScheduleController::class, 'edit'])->name('schedules.edit');
    Route::put('/schedules/{schedule:uuid}', [StreamScheduleController::class, 'update'])->name('schedules.update');
    Route::delete('/schedules/{schedule:uuid}', [StreamScheduleController::class, 'destroy'])->name('schedules.destroy');
    Route::post('/schedules/{schedule:uuid}/broadcast', [StreamScheduleController::class, 'broadcast'])->name('schedules.broadcast');

    Route::post('/streams/{stream:uuid}/chat', [ChatController::class, 'store'])->name('chat.store');
    Route::get('/streams/{stream:uuid}/chat', [ChatController::class, 'index'])->name('chat.index');
});

// Public Routes (for viewers)
Route::get('/watch/{stream:uuid}', function ($stream) {
    return view('streams.watch', compact('stream'));
})->name('streams.watch');
