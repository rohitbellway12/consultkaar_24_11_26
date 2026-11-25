<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AIChatController;

/*
|--------------------------------------------------------------------------
| AI Consultation Assistant Routes (GPT-6 Luna)
|--------------------------------------------------------------------------
|
| Handles 5-minute free AI chat consultation, timer enforcement,
| clinical conversation summaries, and human advisor recommendations.
|
*/

Route::controller(AIChatController::class)->group(function () {
    Route::get('home', 'home');
    Route::post('start-chat', 'startChat');
    Route::post('send-message', 'sendMessage');
    Route::post('end-chat', 'endChat');
    Route::get('history', 'history');
    Route::get('session-messages', 'sessionMessages');
});
