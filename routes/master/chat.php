<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\Api\{
    ChatController,
};

Route::namespace('chat')->group(function () {
    //person
    Route::get('messages', [ChatController::class, 'index']);
    Route::post('store', [ChatController::class, 'store']);
    Route::post('seen', [ChatController::class, 'seen']);
    Route::post('delete', [ChatController::class, 'destroy']);
    Route::get('friends/{id}', [ChatController::class, 'friends']);
    //group
    Route::get('get-users-group', [ChatController::class, 'usersGroup']);
    Route::post('create-group', [ChatController::class, 'createChatGroup']);
    Route::post('create-users-group', [ChatController::class, 'createChatUsersGroup']);
    Route::post('delete-group', [ChatController::class, 'deleteChatGroup']);
    Route::post('delete-user-from-group', [ChatController::class, 'deleteUserFromGroup']);
    //settings
    Route::post('create-settings', [ChatController::class, 'create_chat_settings']);
    Route::post('update-settings', [ChatController::class, 'update_chat_settings']);
    Route::get('get-settings', [ChatController::class, 'get_chat_settings']);
});

