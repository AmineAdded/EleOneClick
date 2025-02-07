<?php

use App\Http\Controllers\Api\Categories\CategoryController;
use App\Http\Controllers\Api\Evaluation\EvaluationController;
use App\Http\Controllers\Api\Taches\TachesController;
use App\Mail\ChangerPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ForgotPassword;
use App\Http\Controllers\Api\ResetPassword;
use App\Http\Controllers\Api\VerifEmail;
use App\Http\Controllers\Api\Employees\ChangerInfosFormateur;
use App\Http\Controllers\Api\ChangePassword;
use App\Http\Controllers\Api\GetUAPpersons;
use App\Http\Controllers\Api\Employees\EmployeesController;
use App\Http\Controllers\Api\SendEmailSignUp;
use App\Http\Controllers\Api\Admin\AdminController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//login
Route::post('/login', [AuthController::class, 'login']);
Route::post('/SignUp', [AuthController::class, 'SignUp']);
Route::post('/sendEmailSignUp',[SendEmailSignUp::class,"sendEmail"]);
Route::get('/verifEmail',[VerifEmail::class,'VerifEmail']);

// Groupe forgot password
Route::group(['prefix'=>'/resetPassword'],function(){
    Route::post('/forgot_password',[ForgotPassword::class,'forgot_password']);
    Route::post('/changer_password',[ResetPassword::class,'reset_password']);
    Route::get('/exist_code/{code}',[ResetPassword::class,'exist_code']);
}); 

//Forgot password pour formateur
Route::post('/changerInfosFormateur/{id}',[ChangerInfosFormateur::class,'changerInfosFormateur']);
Route::post('/ChangerPassword',[ChangePassword::class,'ChangerPassword']);

//Show UAP persons and their evaluations
Route::get('/getUAPpersons/{id}',[GetUAPpersons::class,'getUapPersonnelsWithTasks']);

//Employees
Route::group(['prefix' => '/employees'],function(){
    Route::get('/showEmployees',[EmployeesController::class,"afiicheEmployees"]);
    Route::post('/deleteEmployee/{id}',[EmployeesController::class,'deleteEmployee']);
    Route::put('/updateEmployee/{id}',[EmployeesController::class,"updateEmployee"]);
    Route::post('/createEmployee',[EmployeesController::class,"createEmployee"]);
});

//Evaluation
Route::group(['prefix'=>'/evaluation'],function(){
    Route::get('/showEvaluations',[EvaluationController::class,"showEvaluation"]);
    Route::post('/UpdateEmployees',[EvaluationController::class,"updateEmployee"]);
    Route::get('/getEvaluationTasks/{id}',[EvaluationController::class,"getEvaluationTasks"]);
});

//Taches
Route::group(['prefix'=>'/taches'],function(){
    Route::get('/showTaches',[TachesController::class,'showTaches']);
    Route::post('/deleteTaches/{id}',[TachesController::class,'deleteTaches']);
    Route::post('/addTaches',[TachesController::class,'addTaches']);
});

//Categories
Route::group(['prefix'=>'/categories'],function(){
    Route::get('/showCategories',[CategoryController::class,'showCategories']);
    Route::get('/showUAPs',[CategoryController::class,'showUAPs']);
    Route::post('/deleteCategory/{id}',[CategoryController::class,'deleteCategory']);
    Route::post('/addCategory',[CategoryController::class,'addCategory']);
});

//Admin
Route::group(['prefix'=>'/Admin'],function(){
    Route::get('/count',[AdminController::class,'count']);
    Route::get('/getUapInfos',[AdminController::class,'getUapInfos']);
    Route::get('/getUAPData',[AdminController::class,'getUAPData']);
    Route::get('/getTacheData',[AdminController::class,'getTacheData']);
    Route::get('/getCategoriesData',[AdminController::class,'getCategoriesData']);
    Route::get('/fetchTacheEvaluation',[AdminController::class,'fetchTacheEvaluation']);
    Route::get('/fetchNotifications',[AdminController::class,'fetchNotifications']);
    Route::put('/notificatioIsRead/{id}',[AdminController::class,'notificatioIsRead']);
    Route::get('/showFormateurs',[AdminController::class,'showFormateurs']);
    Route::delete('/deleteFormateur/{id}',[AdminController::class,'deleteFormateur']);
    Route::post('/updateFormateur/{id}',[AdminController::class,'updateFormateur']);
    Route::post('/createTrainer',[AdminController::class,'createTrainer']);
});