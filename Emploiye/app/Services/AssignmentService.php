<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\Project;
use App\Models\User;

class AssignmentService
{
    public function getProjectAssignments(Project $project)
    {
        $assignments = $project->assignments()
            ->with('employee:id,firstname,lastname,email')
            ->orderBy('start_date')
            ->get();

        $assignedIds = $assignments->pluck('employee_id')->toArray();

        $freeEmployees = User::with('role')->get();

        $busyEmployees = User::whereHas('role', fn($q) =>
                $q->where('name', 'employee')
            )
            ->whereHas('assignments', fn($q) =>
                $q->whereHas('project', fn($pq) =>
                    $pq->whereIn('status',['planning','active'])
                        ->where('id','!=',$project->id)
                )
            )
            ->whereNotIn('id',$assignedIds)
            ->with([
                'assignments' => fn($q) =>
                    $q->with('project:id,name,status')
            ])
            ->select('id','firstname','lastname','email')
            ->get()
            ->map(fn($e) => [
                ...$e->toArray(),
                'availability' => 'busy',
                'current_projects' => $e->assignments->map(fn($a) => [
                    'name'   => $a->project->name,
                    'status' => $a->project->status,
                    'role'   => $a->role_in_project,
                ])
            ]);

        return [
            'project' => $project,
            'assignments' => $assignments,
            'free_employees' => $freeEmployees,
            'busy_employees' => $busyEmployees,
        ];
    }

    public function createAssignment(Project $project, array $data)
    {
        $exists = Assignment::where('project_id', $project->id)
            ->where('employee_id', $data['employee_id'])
            ->exists();

        if ($exists) {
            throw new \Exception(
                "Cet employé est déjà assigné à ce projet."
            );
        }

        $assignment = Assignment::create([
            ...$data,
            'project_id' => $project->id,
        ]);

            return $assignment->load(
            'employee:id,firstname,lastname,email'
            );
    }

    public function updateAssignment(Assignment $assignment, array $data)
    {
        $assignment->update($data);

        return $assignment->load(
        'employee:id,firstname,lastname,email'
        );
    }

    public function deleteAssignment(Assignment $assignment)
    {
        return $assignment->delete();
    }
}