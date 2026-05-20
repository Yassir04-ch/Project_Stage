<?php
namespace App\Services;

use App\Models\Project;
use Illuminate\Validation\ValidationException;

class ProjectService
{
    public function create(array $data)
    {
        if (!empty($data['end_date']) && $data['start_date'] > $data['end_date']) {
            throw ValidationException::withMessages([
                'end_date' => 'End date must be after start date',
            ]);
        }

        if (isset($data['budget']) && $data['budget'] < 0) {
            throw ValidationException::withMessages([
                'budget' => 'Budget cannot be negative',
            ]);
        }

        $data['status'] = $data['status'] ?? 'planning';

        $project = Project::create($data);
        return $project;
    }
}