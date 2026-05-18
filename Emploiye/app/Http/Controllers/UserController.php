<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateEmployeeRequest;
use App\Http\Requests\User\UpdateEmployeeRequest;
use App\Services\UserService;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private UserService $userService;
    public function __construct() {
        $this->userService = new UserService();
    }

    public function index(Request $request): JsonResponse
    {
        $employees = $this->userService->getEmployees($request->all());

        return response()->json([
            'success' => true,
            'data'    => $employees,
        ]);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $user->load('role', 'skills', 'assignments.project', 'evaluations'),
        ],200);
    }

    public function store(CreateEmployeeRequest $request): JsonResponse
    {
        $employee = $this->userService->createEmployee($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Employé créé avec succès.',
            'data'    => $employee->load('role'),
        ], 201);
    }

    public function update(UpdateEmployeeRequest $request, User $user): JsonResponse
    {
        $employee = $this->userService->updateEmployee($user, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Employé mis à jour avec succès.',
            'data'    => $employee,
        ],200);
    }

    public function destroy(User $user): JsonResponse
    {
        $this->userService->deleteEmployee($user);

        return response()->json([
            'success' => true,
            'message' => 'Employé supprimé avec succès.',
        ],200);
    }
}