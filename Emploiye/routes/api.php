<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AbsenceController;
use App\Http\Controllers\Api\JustificationController;
use App\Http\Controllers\Api\AssignmentController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-all', [AuthController::class, 'logoutAll']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::get('/users', [AdminController::class, 'users']);
});

Route::get('/getUser/{id}',[AuthController::class, 'GetUser']);

Route::post('/createEmploiyee', [UserController::class, 'store']);
Route::post('/desactiverUser', [UserController::class, 'desactiverUser']);
Route::post('/activerUser', [UserController::class, 'activerUser']);


Route::get('/stats', [AdminController::class, 'stats']);


Route::post('/projects', [ProjectController::class, 'store']);
Route::get('/projects', [ProjectController::class, 'index']);
Route::get('/projects/{id}', [ProjectController::class, 'show']);
Route::put('projects/{project}',[ProjectController::class, 'update']);
Route::patch('projects/{project}/status', [ProjectController::class, 'updateStatus']);
Route::delete('projects/{project}',[ProjectController::class, 'destroy']);


Route::get('projects/{project}/assignments',[AssignmentController::class, 'index']);
Route::post('projects/{project}/assignments',[AssignmentController::class, 'store']);
Route::patch('assignments/{assignment}',[AssignmentController::class, 'update']);
Route::delete('assignments/{assignment}',[AssignmentController::class, 'destroy']);


Route::get('/absences',[AbsenceController::class,'index']);
Route::post('/absences',[AbsenceController::class,'store']);
Route::put('/absences/{id}',[AbsenceController::class,'update']);
Route::delete('/absences/{id}',[AbsenceController::class,'destroy']);


Route::get('/justifications/absence/{absenceId}', [JustificationController::class, 'getByAbsence']);

Route::get('/justifications', [JustificationController::class, 'index']);
Route::post('/justifications', [JustificationController::class, 'store']);
Route::put('/justifications/{id}', [JustificationController::class, 'update']);
Route::delete('/justifications/{id}', [JustificationController::class, 'destroy']);
Route::post('/justifications/{id}/status', [JustificationController::class, 'updateStatus']);