<?php

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

Route::post('users', [UserController::class, 'register']);
Route::post('users/login', [UserController::class, 'login']);
Route::middleware('auth:api')->group(function () {
    Route::get('user', [UserController::class, 'me']);
    Route::put('user', [UserController::class, 'update']);
});
