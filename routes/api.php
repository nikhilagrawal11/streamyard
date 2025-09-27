<?php

use App\Http\Controllers\Api\ParticipantApiController;
use App\Http\Controllers\Api\StreamApiController;
use App\Http\Controllers\Api\VideoApiController;
use App\Http\Controllers\ChatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum'])->group(function () {

    // Stream API
    Route::get('/streams', [StreamApiController::class, 'index']);
    Route::post('/streams', [StreamApiController::class, 'store']);
    Route::get('/streams/{stream:uuid}', [StreamApiController::class, 'show']);
    Route::put('/streams/{stream:uuid}', [StreamApiController::class, 'update']);
    Route::delete('/streams/{stream:uuid}', [StreamApiController::class, 'destroy']);

    Route::post('/streams/{stream:uuid}/start', [StreamApiController::class, 'start']);
    Route::post('/streams/{stream:uuid}/stop', [StreamApiController::class, 'stop']);
    Route::post('/streams/{stream:uuid}/join', [StreamApiController::class, 'join']);
    Route::post('/streams/{stream:uuid}/leave', [StreamApiController::class, 'leave']);

    // Video API
    Route::get('/videos', [VideoApiController::class, 'index']);
    Route::post('/videos', [VideoApiController::class, 'store']);
    Route::get('/videos/{upload:uuid}', [VideoApiController::class, 'show']);
    Route::delete('/videos/{upload:uuid}', [VideoApiController::class, 'destroy']);
    Route::post('/videos/{upload:uuid}/play', [VideoApiController::class, 'play']);

    // Participant API
    Route::post('/streams/{stream:uuid}/participants', [ParticipantApiController::class, 'store']);
    Route::put('/participants/{participant}/settings', [ParticipantApiController::class, 'updateSettings']);
    Route::delete('/participants/{participant}', [ParticipantApiController::class, 'destroy']);

    // WebRTC Signaling
    Route::post('/streams/{stream:uuid}/signal', [StreamApiController::class, 'signal']);
    Route::post('/streams/{stream:uuid}/ice-candidate', [StreamApiController::class, 'iceCandidate']);
    Route::post('/streams/{stream:uuid}/offer', [StreamApiController::class, 'offer']);
    Route::post('/streams/{stream:uuid}/answer', [StreamApiController::class, 'answer']);

    Route::post('/streams/{stream:uuid}/chat', [ChatController::class, 'store']);
    Route::get('/streams/{stream:uuid}/chat', [ChatController::class, 'index']);
});

// Public API endpoints
Route::get('/streams/{stream:uuid}/info', [StreamApiController::class, 'publicInfo']);
Route::get('/streams/{stream:uuid}/status', [StreamApiController::class, 'status']);
