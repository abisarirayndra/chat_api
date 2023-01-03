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
    Route::get('/chat', [App\Http\Controllers\ChatController::class, 'index'])->name('chat');
    Route::get('/account_logout', [App\Http\Controllers\UserController::class, 'logout'])->name('logout');
});
