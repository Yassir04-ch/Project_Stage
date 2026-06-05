<?php

namespace App\Http\Controllers\Api;

use App\Models\Assignment;
use App\Models\Project;
use App\Services\AssignmentService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AssignmentController extends Controller
{
    public function __construct(
        protected AssignmentService $service
    ) {}

    public function index(Project $project)
    {
        return response()->json([
            'data' => $this->service
                ->getProjectAssignments($project)
        ]);
    }

    public function store(Request $request, Project $project)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:users,id',
            'role_in_project' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        try {

            $assignment =
                $this->service->createAssignment(
                    $project,
                    $data
                );

            return response()->json([
                'data' => $assignment
            ],201);

        } catch (\Exception $e) {

            return response()->json([
                'message' => $e->getMessage()
            ],422);

        }
    }

    public function update(
        Request $request,
        Assignment $assignment
    ) {

        $data = $request->validate([
            'role_in_project' => 'sometimes|string|max:255',
            'start_date' => 'sometimes|date',
            'end_date' => 'nullable|date'
        ]);

        return response()->json([
            'data' => $this->service
                ->updateAssignment(
                    $assignment,
                    $data
                )
        ]);
    }

    public function destroy(
        Assignment $assignment
    ) {

        $this->service
            ->deleteAssignment($assignment);

        return response()->json([
            'message' => 'deleted'
        ]);
    }
}