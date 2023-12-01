<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenticateController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('login', [AuthenticateController::class, 'login']);
Route::post('register', [AuthenticateController::class, 'register']);
Route::group(['middleware' => 'auth:sanctum'], function() {
    Route::post('details', [AuthenticateController::class, 'details']);
    Route::post('logout', [AuthenticateController::class, 'logout']);
});

Route::group(['middleware' => 'auth:sanctum'], function() {
    Route::get('showPost/{id}', [PostController::class, 'showPost']);
    Route::post('addPost', [PostController::class, 'addPost']);
    Route::post('updatePost/{id}', [PostController::class, 'updatePost']);
    Route::get('getPosts', [PostController::class, 'getPosts']);
    Route::delete('deletePost/{id}', [PostController::class, 'deletePost']);
});

Route::group(['middleware' => 'auth:sanctum'], function() {
    Route::post('addComment/{postId}', [CommentController::class, 'addComment']);
    Route::post('updateComment/{id}', [CommentController::class, 'updateComment']);
    Route::delete('deleteComment/{id}', [CommentController::class, 'deleteComment']);
    Route::get('getComment', [CommentController::class, 'getComment']);
});

Route::group(['middleware' => 'auth:sanctum'], function() {
    Route::post('addLike/{postId}', [LikeController::class, 'addLike']);
    Route::get('getLikes/{postId}', [LikeController::class, 'getLikes']);
});
