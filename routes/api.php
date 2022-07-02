<?php

use App\Http\Controllers\api\authController;
use App\Http\Controllers\api\BlogController;
use App\Http\Controllers\api\CommentController;
use App\Http\Controllers\api\HomeController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\SubscribController;
use App\Models\Subscriber;
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

// Auth Routes
Route::group(['middleware' => 'guest:sanctum', 'prefix' => 'auth/'], function () {
    Route::post('login/', [authController::class, 'Login'])->name('login');
    Route::post('register/', [authController::class, 'Register'])->name('register');
});

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('auth/logout/', [authController::class, 'Logout']);
});

// Blog Routes
Route::apiResource('blogs', BlogController::class);

// Comments Routes
Route::get('comment/blog/{blog}', [CommentController::class, 'index']);
Route::apiResource('comment', CommentController::class)->except(['index']);

// Categories routes
Route::apiResource('categories', CategoriesController::class);


// Subscrib Routes
Route::post('subscrib', [SubscribController::class, 'subscrib'])->name('subscrib');
Route::post('unsubscrib', [SubscribController::class, 'unSubscrib'])->name('unsubscrib');
