<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController; 
use App\Http\Controllers\ImageController; 
use App\Http\Controllers\machineController; 
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
Route::get('/image/{id}', [ImageController::class, 'getimage']); 
Route::get('/{op}/{pagesize?}/{page?}', [machineController::class, 'MachinePieces'])->middleware('jwt'); 
Route::delete('/machines/{id}', [machineController::class, 'deleteMachine']); 
Route::post('/machines', [machineController::class, 'CreatMachine']); 

Route::any('{any}', function(){
    return data([], 400);
})->middleware('jwt');
