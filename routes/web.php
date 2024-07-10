<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [AuthController::class, "login"]);
Route::get('/login_otp', [AuthController::class, "login_otp"]);
Route::post('/login_otp_action', [AuthController::class, "login_otp_action"]);
Route::get('/otp/{user_id}', [AuthController::class, "otp"]);
Route::post('/otp_action', [AuthController::class, "otp_action"]);
Route::get('/admin', [AdminController::class, "index"]);
