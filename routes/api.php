<?php

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
Route::post('/account_register',[App\Http\Controllers\UserController::class, 'store'])->name('account_register');
Route::post('/account_login',[App\Http\Controllers\UserController::class, 'login'])->name('account_login');

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/account_logout', [App\Http\Controllers\UserController::class, 'logout'])->name('logout');
    Route::get('/user_list', [App\Http\Controllers\ChatController::class, 'index'])->name('user_list');
    Route::get('/chat/with', [App\Http\Controllers\ChatController::class, 'chatWith'])->name('chat.with');
    Route::post('/chat/send_to', [App\Http\Controllers\ChatController::class, 'sendMessage'])->name('chat.send_to');
    Route::get('/latest_conversation_list', [App\Http\Controllers\ChatController::class, 'latestConversation'])->name('conversation_list');
});
