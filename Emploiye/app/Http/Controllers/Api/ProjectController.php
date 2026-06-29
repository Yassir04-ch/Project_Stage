<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Services\ProjectService;
use App\Http\Requests\ProjectRequest;
use Illuminate\Http\Request;
use App\Models\Project;
use Illuminate\Http\JsonResponse;

class ProjectController extends Controller{
    private ProjectService $service;

    public function __construct()
    {
        $this->service = new ProjectService();
    }

    public function index(){
        $projects = Project::with('assignments')->get();
        return response()->json([
            'message'=>'all projects',
            'data'=>$projects
        ],200);
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

    public function show($id)
    {
        $project = $this->service->getProjectById($id);

        return response()->json([
            'message' => 'project details',
            'data' => $project
        ],200);
    }

        public function update(Request $request, Project $project)
    {
        $data = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'budget'      => 'nullable|numeric|min:0',
            'start_date'  => 'sometimes|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'status'      => 'sometimes|in:planning,active,completed,cancelled',
        ]);

        $project->update($data);

        return response()->json(['data' => $project]);
    }

    public function updateStatus(Request $request, Project $project)
    {
        $request->validate([
            'status' => 'required|in:planning,active,completed,cancelled',
        ]);

        $project->update(['status' => $request->status]);

        return response()->json(['data' => $project]);
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return response()->json(['message' => 'Project deleted successfully']);
    }

    public function getProjectEmployees($projectId)
    {
        $project = Project::with(['assignments.employee'])->findOrFail($projectId);

        $employeesList = [];

        foreach ($project->assignments as $assignment) {
            if ($assignment->employee) {
                $employeesList[] = [
                    'id'          => $assignment->employee->id,
                    'firstname'   => $assignment->employee->firstname,
                    'lastname'    => $assignment->employee->lastname,
                    'email'       => $assignment->employee->email,
                    'role_in_project' => $assignment->role_in_project, 
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data'    => $employeesList
        ]);
    }


}