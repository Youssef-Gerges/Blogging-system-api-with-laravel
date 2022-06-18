<?php

use App\Http\Controllers\api\authController;
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

Route::group(['middleware'=>'guest:sanctum', 'prefix'=>'auth/'], function (){
    Route::post('login/', [authController::class, 'Login'])->name('login');
    Route::post('register/', [authController::class, 'Register'])->name('register');
});

Route::group(['middleware' => 'auth:sanctum'], function(){
    Route::post('auth/logout/', [authController::class, 'Logout']);
});

