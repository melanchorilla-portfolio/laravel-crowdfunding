<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;

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

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/verification', [AuthController::class, 'verification']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::post('/regenerate-otp-code', [AuthController::class, 'regenerateOtpCode']);
    Route::post('/update-password', [AuthController::class, 'updatePassword'])->middleware('auth:api', 'email_verification', 'is_admin');
});

Route::get('/get-profile', [\App\Http\Controllers\Api\UserController::class, 'getProfile'])->middleware('auth:api', 'email_verification');
Route::post('/update-profile', [\App\Http\Controllers\Api\UserController::class, 'updateProfile'])->middleware('auth:api', 'email_verification');
Route::apiResource('/campaign', 'App\Http\Controllers\Api\CampaignController');


