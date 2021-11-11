<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// v1 of api
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
    changePasswordController
};


use App\Http\Controllers\versionappController;


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
    // information user by id
    Route::get('usersByPhone/{string?}', [userController::class, 'getUsersByPhone']);
    Route::get('statusUser', [userController::class, 'GetUserStatus']);
    Route::get('getUserIdByAuth', [userController::class, 'getUserIdByAuth']);
    Route::get('userInformation/{id?}/{group_post_id?}', [userController::class, 'getInformationUser'])->whereNumber('id', 'group_post_id')->name('userInformationApi');
    Route::get('searchForUser/{name?}', [userController::class, 'searchForUser']);
    Route::get('searchGlobal/{name?}', [userController::class, 'searchGlobal']);
    Route::put('updateUser', [userController::class, 'update']);
    Route::put('changePassword', [changePasswordController::class, 'index']);
    Route::get('getProfiles/{name?}', [userController::class, 'getProfiles'])->where('name', '[A-Za-z]+');
    Route::get('pushNotificarionForSingleUser/{$user_id}', [userController::class, 'pushNotificarionForSingleUser'])->whereNumber('user_id');

    // innovation
    Route::get('getInnovationByDomain/{id}', [innovationController::class, 'getInnovationByDomain'])->whereNumber('id')->name('getInnovationByDomainApi');
    Route::post('funding', [innovationController::class, 'funding'])->name('fundingApi');
    Route::post('handleActionInnovation', [innovationController::class, 'handleActionInnovation']);
    Route::get('likeListByInnovation/{innovation_id}', [innovationController::class, 'likeListByInnovation']);

    Route::resource('innovationDomains', innovationdomainController::class);
    Route::resource('innovations', innovationController::class);
    //group & post of group & group universe
    Route::get('infoGroup/{group_id?}', [GroupController::class, 'infoGroup'])->whereNumber('group_id');
    Route::get('getTheLatestPost', [GroupPostController::class, 'getTheLatestPost']);
    Route::get('deleteCommentFromPost/{id_comment?}/{group_post_id?}', [GroupPostController::class, 'deleteCommentFromPost'])->whereNumber('id_comment', 'group_post_id');
    Route::get('getposts/{id}', [GroupPostController::class, 'getPostsByCategory'])->whereNumber('id');
    Route::post('addcommentpost', [GroupPostController::class, 'addComment']);
    Route::post('replayToComment', [GroupPostController::class, 'replayToComment']);
    Route::post('handleActionPost', [GroupPostController::class, 'hanldeAction']);
    Route::get('groupbyunviers/{id}', [GroupController::class, 'getgroupsByunivers'])->whereNumber('id');
    Route::get('likeListByPost/{id}', [GroupPostController::class, 'likeListByPost'])->whereNumber('id');
    Route::get('commentsByPost/{id}', [GroupPostController::class, 'commentsByPost'])->whereNumber('id');
    Route::get('getPostsNotApprovedByCategory/{id}', [GroupPostController::class, 'getPostsNotApprovedByCategory'])->whereNumber('id');
    Route::get('approveAllPosts/{id}', [GroupPostController::class, 'approveAllPosts'])->whereNumber('id');
    Route::post('decilnePost', [GroupPostController::class, 'decilnePost'])->whereNumber('id');
    Route::post('approvePost', [GroupPostController::class, 'approvePost'])->whereNumber('id');
    Route::get('checkUserIsAdminOfGroup/{group_id}', [GroupPostController::class, 'checkUserIsAdminOfGroup'])->whereNumber('group_id');
    Route::get('searchGroup/{name?}', [GroupController::class, 'searchGroup']);
    Route::put('updateComment/{comment_id?}', [GroupPostController::class, 'updateComment'])->whereNumber('comment_id');
    Route::resource('group', GroupController::class);
    Route::resource('grouposts', GroupPostController::class);
    Route::resource('groupuniverses', GroupUniverseController::class);

    // amana
    Route::get('amanaByCategory/{id}', [AmanaController::class, 'amanaByCategory']);
    Route::resource('amana', AmanaController::class);
    Route::resource('amanaCategory', AmanaCategoryController::class);
    // job
    // freelance
    Route::resource('freelance', FreelanceController::class);
    // job offer
    Route::get('getAllJobs', [JobOfferController::class, 'getAll']);
    Route::resource('jobOffer', JobOfferController::class);
    // cv library
    Route::resource('cvLibrary', CvLibraryController::class);

    //Category
    Route::get('listingByCategory/{id}/{pos}', [ListingController::class, 'listingByCategory'])->whereNumber('id', 'pos');
    Route::resource('categories', CategoryController::class);
    Route::resource('listings', ListingController::class);
    // report
    Route::resource('report', ReportController::class);
    // report ITA
    Route::resource('reportIta', ReportItaController::class);
    // follow
    Route::get('acceptFriend/{user_id}', [FollowerController::class, 'AcceptFriend'])->whereNumber('user_id');
    Route::resource('follow', FollowerController::class);
    Route::resource('followGroup', FollowGroupController::class);
    // notification
    Route::get('getNotificationsNotRead', [NotificationController::class, 'getNotificationsNotRead']);
    Route::get('getNotifications', [NotificationController::class, 'getNotifications']);
    Route::get('friendsAccepted/{notification_id?}', [NotificationController::class, 'friendsAccepted'])->whereNumber('notification_id');
    Route::get('getPureNotifcation/{notification_id}', [NotificationController::class, 'getPureNotifcation'])->whereNumber('notification_id');
    Route::get('sendPushNotification/{notification_id}', [NotificationController::class, 'sendPushNotification'])->whereNumber('notification_id');
    Route::get('InteractWithFriend/{user_id}/{status}', [NotificationController::class, 'InteractWithFriend'])->whereNumber('user_id', 'status');
    Route::get('getNotificationById/{id}/{type}', [NotificationController::class, 'getNotificationById'])->whereNumber('id', 'type');
    Route::get('getAddFriends', [NotificationController::class, 'getAddFriends']);
    Route::get('updateRead/{id}', [NotificationController::class, 'updateRead'])->whereNumber('id');
    Route::resource('notification', NotificationController::class);
    //job
    require_once __DIR__ . "/master/jobs.php";
    //chat
    require_once __DIR__ . "/master/chat.php";
});
