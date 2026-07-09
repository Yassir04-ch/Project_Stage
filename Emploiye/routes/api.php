<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AbsenceController;
use App\Http\Controllers\Api\JustificationController;
use App\Http\Controllers\Api\AssignmentController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\SkillController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ServiceController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/getUser/{id}', [AuthController::class, 'GetUser']);
Route::post('/createEmploiyee', [UserController::class, 'store']);

Route::post('/contact', [ContactController::class, 'send']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-all', [AuthController::class, 'logoutAll']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);

    Route::get('/my-assignments', [AssignmentController::class, 'myAssignments']);
    Route::get('/my-absences', [AbsenceController::class, 'myAbsences']);
    Route::post('/absences', [AbsenceController::class, 'store']);

    Route::get('/justifications/absence/{absenceId}', [JustificationController::class, 'getByAbsence']);
    Route::post('/justifications', [JustificationController::class, 'store']);

    Route::get('/skills', [SkillController::class, 'index']);

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::put('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);

    Route::post('/broadcasting/auth', function (\Illuminate\Http\Request $request) {
        return Broadcast::auth($request);
    });

    Route::get('/projects', [ProjectController::class, 'index']);
    Route::get('/projects/{id}', [ProjectController::class, 'show']);

    Route::get('/services', [ServiceController::class, 'index']);
    Route::get('/services/{service}', [ServiceController::class, 'show']);


    // Administrateur + Manager
    Route::middleware('role:Administrateur,Manager')->group(function () {
        Route::get('/stats', [AdminController::class, 'stats']);

        Route::post('/projects', [ProjectController::class, 'store']);
        Route::put('projects/{project}', [ProjectController::class, 'update']);
        Route::patch('projects/{project}/status', [ProjectController::class, 'updateStatus']);

        Route::get('projects/{project}/assignments', [AssignmentController::class, 'index']);
        Route::post('projects/{project}/assignments', [AssignmentController::class, 'store']);
        Route::patch('assignments/{assignment}', [AssignmentController::class, 'update']);
        Route::delete('assignments/{assignment}', [AssignmentController::class, 'destroy']);
    });


    // Administrateur uniquement
    Route::middleware('role:Administrateur')->group(function () {
        Route::delete('projects/{project}', [ProjectController::class, 'destroy']);

        Route::post('/services', [ServiceController::class, 'store']);
        Route::put('/services/{service}', [ServiceController::class, 'update']);
        Route::delete('/services/{service}', [ServiceController::class, 'destroy']);
    });


    // Administrateur + Ressources Humaines
    Route::middleware('role:Administrateur,Ressources Humaines')->group(function () {
        Route::put('/updateUser/{id}', [UserController::class, 'update']);
        Route::put('/users/{id}/activer', [UserController::class, 'activerUser']);
        Route::put('/users/{id}/desactiver', [UserController::class, 'desactiverUser']);

        Route::post('/skills', [SkillController::class, 'store']);
        Route::delete('/skills/{skill}', [SkillController::class, 'destroy']);
    });


    // Administrateur  + Ressources Humaines
    Route::get('/emploiyee', [UserController::class, 'emploiyee']);
    Route::middleware('role:Administrateur,Ressources Humaines')->group(function () {
        Route::get('/users', [AdminController::class, 'users']);

        Route::get('/projects/{id}/employees', [ProjectController::class, 'getProjectEmployees']);

        Route::get('/absences', [AbsenceController::class, 'index']);
        Route::put('/absences/{id}', [AbsenceController::class, 'update']);
        Route::delete('/absences/{id}', [AbsenceController::class, 'destroy']);

        Route::get('/justifications', [JustificationController::class, 'index']);
        Route::put('/justifications/{id}', [JustificationController::class, 'update']);
        Route::delete('/justifications/{id}', [JustificationController::class, 'destroy']);
        Route::put('/justifications/{id}/status', [JustificationController::class, 'updateStatus']);

        Route::get('/users/{user}/skills', [SkillController::class, 'getEmployeeSkills']);
        Route::post('/users/{user}/skills', [SkillController::class, 'assignToEmployee']);

        Route::post('/services/{service}/assign', [ServiceController::class, 'assignEmployee']);
        Route::post('/services/{service}/remove-employee', [ServiceController::class, 'removeEmployee']);
    });

});