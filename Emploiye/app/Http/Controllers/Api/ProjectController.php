<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Services\ProjectService;
use App\Http\Requests\ProjectRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProjectController extends Controller{
    private ProjectService $service;

    public function __construct()
    {
        $this->service = new ProjectService();
    }

    public function store(ProjectRequest $request):JsonResponse
    {
        $validated = $request->validated();

        $project = $this->service->create($validated);

        return response()->json([
            'message' => 'Project created successfully',
            'data' => $project
        ], 201);
    }
}