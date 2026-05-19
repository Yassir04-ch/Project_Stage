<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\UserController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',[AuthController::class, 'login']);

Route::get('/profile',[AuthController::class, 'profile']);
Route::post('/logout',[AuthController::class, 'logout']);
Route::post('/logout-all',[AuthController::class, 'logoutAll']);
Route::post('/change-password',[AuthController::class, 'changePassword']);

Route::post('/createEmploiyee', [UserController::class, 'store']);

