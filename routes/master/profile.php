<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\Api\{
    ProfileController
};
Route::get('get-profile-data', [ProfileController::class, 'getProfileData']);
Route::put('update-profile-data', [ProfileController::class, 'updateProfileData']);
Route::put('update-password', [ProfileController::class, 'updatePassword']);
Route::put('update-profile-picture', [ProfileController::class, 'updateProfilePicture']);
Route::get('get-all-publications', [ProfileController::class, 'getAllPublications']);
