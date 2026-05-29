<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Api\AbsenceController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/profile',[AuthController::class, 'profile']);
Route::post('/logout',[AuthController::class, 'logout']);
Route::post('/logout-all',[AuthController::class, 'logoutAll']);
Route::post('/change-password',[AuthController::class, 'changePassword']);

Route::post('/createEmploiyee', [UserController::class, 'store']);

Route::get('/users', [AdminController::class, 'users']);

Route::get('/stats', [AdminController::class, 'stats']);

Route::post('/projects', [ProjectController::class, 'store']);
Route::get('/projects', [ProjectController::class, 'index']);


Route::get('/absences',[AbsenceController::class,'index']);
Route::post('/absences',[AbsenceController::class,'store']);
Route::put('/absences/{id}',[AbsenceController::class,'update']);
Route::delete('/absences/{id}',[AbsenceController::class,'destroy']);
