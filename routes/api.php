<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VccController;


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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post('/onboard-user', [AuthController::class, 'createUser']);
Route::post('/login', [AuthController::class, 'loginUser']);
// Route::post('/user', [UserController::class, 'store']);



Route::middleware('auth:sanctum')->group(function () {
    
    Route::get('/user', [UserController::class, 'index']);
    Route::POST('/rotate-api-keys', [UserController::class, 'rotateapikeys']);
    
    // Route::put('/user', [UserController::class, 'update']);


    Route::post('/virtual-card', [VccController::class, 'store']);
    Route::get('/virtual-card/{card_id}', [VccController::class, 'show']);
    Route::get('/virtual-card/{card_id}/transactions', [VccController::class, 'transactions']);
    Route::put('/virtual-card/{card_id}', [VccController::class, 'update']);
    Route::delete('/virtual-card/{card_id}', [VccController::class, 'destroy']);
    Route::post('/virtual-card', [VccController::class, 'store']);
});











