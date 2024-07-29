<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController; 
use App\Http\Controllers\ImageController; 
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
Route::post('/login', [UserController::class, 'LOGIN']);
Route::delete('/logout', [UserController::class, 'Delete'])->middleware('jwt');
Route::get('/image/{id}', [ImageController::class, 'getimage'])->middleware('jwt');

Route::any('{any}', function(){
    return data([], 400);
})->middleware('jwt');
