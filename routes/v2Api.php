<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\V1\Api\{
    loginController,
    registerController,
    countryController,
    willayaController,
    forgetpasswordController,
    userController,
    innovationdomainController,
    innovationController,
    GroupController,
    GroupPostController,
    GroupUniverseController,
    AmanaController,
    FreelanceController,
    CategoryController,
    ListingController,
    ReportController,
    AmanaCategoryController,
    JobOfferController,
    CvLibraryController,
    ReportItaController,
    FollowerController,
    FollowGroupController,
    NotificationController,
    changePasswordController,
    ProfileController,
};


use App\Http\Controllers\versionappController;

Route::get('appversion/{version}', [versionappController::class, 'index']);

Route::get('changePath', [userController::class, 'changePath']);

Route::post('login', [loginController::class, 'index'])->name('loginApi');
Route::post('register', [registerController::class, 'index'])->name('registerApi');
Route::post('faceDetection', [registerController::class, 'HandleFaceDetection'])->name('faceVerificationApi');
Route::get('testtest', [userController::class, 'test']);
Route::get('allCountries', [countryController::class, 'index'])->name('allCountriesApi');
Route::get('allWilayas/{id}', [willayaController::class, 'index'])->whereNumber('id')->name('allWillayasApi');
Route::post('forgetpassword', [forgetpasswordController::class, 'index']);
Route::post('verify', [forgetpasswordController::class, 'verify']);
Route::get('getVerificationByUser/{id}', [userController::class, 'getVerificationByUser'])->whereNumber('id');
Route::get('checkifapproved/{id}', [userController::class, 'checkIfApproved'])->whereNumber('id');
Route::get('approve/{id}', [userController::class, 'approve'])->whereNumber('id');

Route::get('usersnotverified', [userController::class, 'getUsersNotVeirifed']);
Route::get('getAllUsersIds/{notification_id}', [userController::class, 'getAllUsersIds'])->whereNumber('notification_id');
Route::get('getCountOfUsersAccepted', [userController::class, 'getCountOfUsersAccepted']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    // user
    Route::get('usersByPhone/{string?}', [userController::class, 'getUsersByPhone']);
    Route::get('getNotificationsNotRead', [NotificationController::class, 'getNotificationsNotRead']);
    Route::resource('follow', FollowerController::class);
    // groups and posts
    Route::get('getOwnGroups', [GroupController::class, 'getOwnGroups']);
    Route::get('getRandomGroups', [GroupController::class, 'getRandomGroups']);
    Route::resource('group', GroupController::class);
    Route::get('getposts/{group_id?}', [GroupPostController::class, 'getPostsbyGroup'])->whereNumber('group_id');
    
    Route::resource('grouposts', GroupPostController::class);
    // search
    Route::get('searchGlobal/{name?}', [userController::class, 'searchGlobal']);
    Route::get('getNotifications', [NotificationController::class, 'getNotifications2']);
    // notification
    Route::get('getPureNotifcation/{notification_id}', [NotificationController::class, 'getPureNotifcation'])->whereNumber('notification_id');
    Route::get('getNotificationById/{id}/{type}', [NotificationController::class, 'getNotificationById'])->whereNumber('id', 'type');
    ////jobs
    Route::post('create-cv', [CvLibraryController::class, 'store']);
    Route::get('get-cv', [CvLibraryController::class, 'index']);
    Route::post('cv-increment', [CvLibraryController::class, 'increment']);
    Route::post('create-offer', [JobOfferController::class, 'store']);
    Route::post('change-status-offer', [JobOfferController::class, 'changeStatus']);
    Route::get('get-offers', [JobOfferController::class, 'index']);
    Route::post('create-freelance', [FreelanceController::class, 'store']);
    Route::post('change-status-freelance', [FreelanceController::class, 'changeStatus']);
    Route::get('get-freelances', [FreelanceController::class, 'index']);
    //profile
    Route::get('get-profile-data', [ProfileController::class, 'getProfileData']);
    Route::post('update-profile-data', [ProfileController::class, 'updateProfileData']);
    Route::post('update-profile-picture', [ProfileController::class, 'updateProfilePicture']);
    Route::get('get-all-publications', [ProfileController::class, 'getAllPublications']);
});
