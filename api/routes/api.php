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
Route::delete('/machines/{id}', [machineController::class, 'deleteMachine'])->middleware('jwt'); 
Route::put('/machines/{id}', [machineController::class, 'updateMachine'])->middleware('jwt'); 
Route::post('/machines', [machineController::class, 'CreatMachine'])->middleware('jwt'); 
Route::get('/search/{category?}/{q?}/{pageSize?}/{page?}', [machineController::class, 'Search'])->middleware('jwt');

Route::get('/motherboards/{pagesize?}/{page?}', [machineController::class, 'MachinePieces'])->middleware('jwt'); 
Route::get('/processors/{pagesize?}/{page?}', [machineController::class, 'MachinePieces'])->middleware('jwt'); 
Route::get('/ram-memories/{pagesize?}/{page?}', [machineController::class, 'MachinePieces'])->middleware('jwt'); 
Route::get('/storage-devices/{pagesize?}/{page?}', [machineController::class, 'MachinePieces'])->middleware('jwt'); 
Route::get('/graphic-cards/{pagesize?}/{page?}', [machineController::class, 'MachinePieces'])->middleware('jwt'); 
Route::get('/power-supplies/{pagesize?}/{page?}', [machineController::class, 'MachinePieces'])->middleware('jwt'); 
Route::get('/machines/{pagesize?}/{page?}', [machineController::class, 'MachinePieces'])->middleware('jwt'); 
Route::get('/brands/{pagesize?}/{page?}', [machineController::class, 'MachinePieces'])->middleware('jwt'); 

Route::any('{any}', function(){
    return data([], 400);
})->middleware('jwt');
