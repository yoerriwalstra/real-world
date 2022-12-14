<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
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

Route::middleware('auth:api')->group(function () {
    Route::get('user', [UserController::class, 'me']);
    Route::put('user', [UserController::class, 'update']);

    Route::post('profiles/{username}/follow', [ProfileController::class, 'follow'])
        ->whereAlphaNumeric('username')
        ->middleware('can:follow-profile,username'); // `follow-profile` Gate is registered in AuthServiceProvider
    Route::delete('profiles/{username}/follow', [ProfileController::class, 'unfollow'])
        ->whereAlphaNumeric('username');

    Route::get('articles/feed', [ArticleController::class, 'feed']); // http://localhost/api/articles/feed
    Route::post('articles', [ArticleController::class, 'create']);
    Route::put('articles/{article}', [ArticleController::class, 'update'])->middleware('can:update-article,article');
    Route::delete('articles/{article}', [ArticleController::class, 'delete'])->middleware('can:update-article,article');
    Route::post('articles/{article}/favorite', [ArticleController::class, 'favorite']);
    Route::delete('articles/{article}/favorite', [ArticleController::class, 'unfavorite']);

    Route::post('articles/{article}/comments', [CommentController::class, 'create']);
    Route::delete('articles/{article}/comments/{comment}', [CommentController::class, 'delete'])->middleware('can:delete-comment,comment');
});

Route::post('users', [UserController::class, 'register']);
Route::post('users/login', [UserController::class, 'login']);

Route::get('profiles/{username}', [ProfileController::class, 'get'])
    ->whereAlphaNumeric('username');

Route::get('articles', [ArticleController::class, 'get']);
Route::get('articles/{article}', [ArticleController::class, 'getOne']);

Route::get('articles/{article}/comments', [CommentController::class, 'get']);

Route::get('tags', [TagController::class, 'get']);
