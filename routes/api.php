<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Routes for guests only
Route::middleware('guest')->group(function (){
    Route::post('login', [AuthController::class, 'login']);
});


Route::middleware('auth:sanctum')->group(function (){
    Route::get('/user', [UserController::class, 'getUser']);

    // Chats
    Route::post('send-message', [ChatController::class, 'sendMessage']);
    Route::get('chats', [ChatController::class, 'getUserChats']);
    Route::get('chats/{id}/messages', [ChatController::class, 'getChatMessages']);
    Route::put('chats/{id}/markAsRead', [ChatController::class, 'markAsRead']);
    Route::delete('messages/{id}', [ChatController::class, 'destroy']);
});

