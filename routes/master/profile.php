<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\Api\{
    ProfileController
};

Route::get('get-profile-data', [ProfileController::class, 'getProfileData']);
Route::post('update-profile-data', [ProfileController::class, 'updateProfileData']);
Route::post('update-password', [ProfileController::class, 'updatePassword']);
Route::post('update-profile-picture', [ProfileController::class, 'updateProfilePicture']);
Route::get('get-all-publications', [ProfileController::class, 'getAllPublications']);
