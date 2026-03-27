<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RfidController;

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
Route::middleware('api.key')->prefix('rfid')->group(function () {
    Route::post('/register', [RfidController::class, 'register']);
    Route::post('/checkin',  [RfidController::class, 'checkin']);
    Route::get('/status/{uid}', [RfidController::class, 'status']);
});
