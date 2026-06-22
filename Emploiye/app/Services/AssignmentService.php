<?php

namespace App\Services;

use App\Mail\EmployeeNotificationMail;
use App\Models\Assignment;
use App\Models\Notification;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

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

        $busyEmployees = User::whereHas('role', fn ($q) =>
                $q->where('name', 'employee')
            )
            ->whereHas('assignments', fn ($q) =>
                $q->whereHas('project', fn ($pq) =>
                    $pq->whereIn('status', ['planning', 'active'])
                       ->where('id', '!=', $project->id)
                )
            )
            ->whereNotIn('id', $assignedIds)
            ->with([
                'assignments' => fn ($q) =>
                    $q->with('project:id,name,status')
            ])
            ->select('id', 'firstname', 'lastname', 'email')
            ->get()
            ->map(fn ($e) => [
                ...$e->toArray(),
                'availability' => 'busy',
                'current_projects' => $e->assignments->map(fn ($a) => [
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

        $employee = User::findOrFail($data['employee_id']);

        $this->notifyUser(
            $employee,
            'project_assigned',
            'Nouvelle affectation',
            "Vous avez été affecté au projet {$project->name} en tant que {$data['role_in_project']}.",
            [
                'project_id'     => $project->id,
                'project_name'   => $project->name,
                'assignment_id'  => $assignment->id,
                'role_in_project'=> $data['role_in_project'],
            ]
        );

        return $assignment->load(
            'employee:id,firstname,lastname,email'
        );
    }

    public function updateAssignment(Assignment $assignment,array $data)
    {
        $assignment->update($data);

        if ($assignment->employee) {

            $this->notifyUser(
                $assignment->employee,
                'assignment_updated',
                'Affectation modifiée',
                'Votre affectation à un projet a été mise à jour.',
                [
                    'assignment_id' => $assignment->id,
                    'project_id'    => $assignment->project_id,
                ]
            );
        }

        return $assignment->load(
            'employee:id,firstname,lastname,email'
        );
    }

    public function deleteAssignment(
        Assignment $assignment
    ) {
        $employee = $assignment->employee;

        if ($employee) {
            $this->notifyUser(
                $employee,
                'assignment_deleted',
                'Affectation supprimée',
                'Votre affectation au projet a été supprimée.',
                [
                    'assignment_id' => $assignment->id,
                    'project_id'    => $assignment->project_id,
                ]
            );
        }

        return $assignment->delete();
    }

    private function notifyUser(User $user,string $type,string $title,string $message,array $data = []): void 
    {

        Notification::create([
            'user_id' => $user->id,
            'type'    => $type,
            'title'   => $title,
            'message' => $message,
            'data'    => $data,
            'is_read' => false,
        ]);

        Mail::to($user->email)->send(
            new EmployeeNotificationMail(
                $title,
                $message
            )
        );
    }
}