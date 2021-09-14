<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
  versionappController,
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
  ReportItaController
};


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



Route::get('appversion/{version}',[versionappController::class,'index']);

Route::post('login',[loginController::class,'index']);
Route::post('register',[registerController::class,'index']);
Route::post('faceDetection',[registerController::class,'HandleFaceDetection']);

Route::get('allCountries',[countryController::class,'index']);
Route::get('allWilayas/{id}',[willayaController::class,'index'])->whereNumber('id');
Route::post('forgetpassword',[forgetpasswordController::class,'index']);
Route::post('verify',[forgetpasswordController::class,'verify']);
Route::get('getVerificationByUser/{id}',[userController::class,'getVerificationByUser'])->whereNumber('id');
Route::get('checkifapproved/{id}',[userController::class,'checkIfApproved'])->whereNumber('id');
Route::get('approve/{id}',[userController::class,'approve'])->whereNumber('id');

Route::get('usersnotverified',[userController::class,'getUsersNotVeirifed']);

Route::group(['middleware' => 'auth:sanctum'], function(){
    // innovation
    Route::resource('innovationDomains', innovationdomainController::class);
    Route::resource('innovations', innovationController::class);
    Route::get('getInnovationByDomain/{id}',[innovationController::class,'getInnovationByDomain'])->whereNumber('id');
    Route::post('funding',[innovationController::class,'funding']);
    Route::post('handleActionInnovation',[innovationController::class,'handleActionInnovation']);
    Route::get('likeListByInnovation/{innovation_id}',[innovationController::class,'likeListByInnovation']);
    //group & post of group & group universe
    Route::resource('group', GroupController::class);
    Route::resource('groupuniverse', GroupUniverseController::class);
    Route::resource('grouposts', GroupPostController::class);
    Route::get('getposts/{id}',[GroupPostController::class,'getPostsByCategory'])->whereNumber('id');
    Route::post('addcommentpost',[GroupPostController::class,'addComment']);
    Route::post('handleActionPost',[GroupPostController::class,'hanldeAction']);
    Route::resource('groupuniverses', GroupUniverseController::class);
    Route::get('groupbyunviers/{id}',[GroupController::class,'getgroupsByunivers'])->whereNumber('id');
    Route::get('likeListByPost/{id}',[GroupPostController::class,'likeListByPost'])->whereNumber('id');
    Route::get('commentsByPost/{id}',[GroupPostController::class,'commentsByPost'])->whereNumber('id');
    // amana
    Route::resource('amana', AmanaController::class);
    Route::resource('amanaCategory', AmanaCategoryController::class);
    Route::get('amanaByCategory/{id}', [AmanaController::class,'amanaByCategory']);
    // job
      // freelance
      Route::resource('freelance', FreelanceController::class);
      // job offer
      Route::resource('jobOffer', JobOfferController::class);
      Route::get('getAllJobs', [JobOfferController::class,'getAll']);
      // cv library
      Route::resource('cvLibrary', CvLibraryController::class);
    
    //Category
    Route::resource('categories', CategoryController::class);
    Route::resource('listings', ListingController::class);
    Route::get('listingByCategory/{id}/{pos}',[ListingController::class,'listingByCategory'])->whereNumber('id','pos');
    // report
    Route::resource('report', ReportController::class);
    Route::post('reporttest', [ReportController::class,'test']);
      // report ITA
      Route::resource('reportIta', ReportItaController::class);
    });