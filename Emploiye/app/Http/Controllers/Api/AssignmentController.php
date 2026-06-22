<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Project;
use App\Services\AssignmentService;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function __construct(
        protected AssignmentService $service
    ) {}

    public function index(Project $project)
    {
        return response()->json([
            'data' => $this->service->getProjectAssignments($project)
        ]);
    }

    public function store(Request $request, Project $project)
    {
        $data = $request->validate([
            'employee_id'    => 'required|exists:users,id',
            'role_in_project'=> 'required|string|max:255',
            'start_date'     => 'required|date',
            'end_date'       => 'nullable|date|after_or_equal:start_date',
        ]);

        try {

            $assignment = $this->service->createAssignment(
                $project,
                $data
            );

            return response()->json([
                'success' => true,
                'message' => 'Employé affecté avec succès.',
                'data'    => $assignment
            ], 201);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);

        }
    }

    public function update(Request $request, Assignment $assignment)
    {
        $data = $request->validate([
            'role_in_project' => 'sometimes|string|max:255',
            'start_date'      => 'sometimes|date',
            'end_date'        => 'nullable|date',
        ]);

        try {

            $updatedAssignment = $this->service->updateAssignment(
                $assignment,
                $data
            );

            return response()->json([
                'success' => true,
                'message' => 'Affectation mise à jour avec succès.',
                'data'    => $updatedAssignment
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);

        }
    }

    public function destroy(Assignment $assignment)
    {
        try {

            $this->service->deleteAssignment($assignment);

            return response()->json([
                'success' => true,
                'message' => 'Affectation supprimée avec succès.'
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);

        }
    }
}