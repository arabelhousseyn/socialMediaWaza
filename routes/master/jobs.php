<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\Api\{
    CvLibraryController,
    JobOfferController,
    FreelanceController
};

Route::post('create-cv', [CvLibraryController::class, 'store']);
Route::get('get-cv', [CvLibraryController::class, 'index']);
Route::get('get-all-cv', [CvLibraryController::class, 'getAllCv']);
Route::post('cv-increment', [CvLibraryController::class, 'increment']);
Route::post('create-offer', [JobOfferController::class, 'store']);
Route::post('change-status-offer', [JobOfferController::class, 'changeStatus']);
Route::get('get-offers', [JobOfferController::class, 'index']);
Route::post('create-freelance', [FreelanceController::class, 'store']);
Route::post('change-status-freelance', [FreelanceController::class, 'changeStatus']);
Route::get('get-freelances', [FreelanceController::class, 'index']);
