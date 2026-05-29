<?php

namespace App\Services;

use App\Models\Absence;

class AbsenceService
{
    public function getAll()
    {
        return Absence::with('user.role')->latest()->get();
    }

    public function create(array $data)
    {
        return Absence::create([
            'user_id' => $data['user_id'],
            'date' => $data['date'],
            'status' => $data['status'],
            'check_in' => $data['check_in'] ?? null,
            'check_out' => $data['check_out'] ?? null,
            'note' => $data['note'] ?? null,
            'is_justified' => $data['is_justified'] ?? false,
        ]);
    }

    public function update($id,array $data)
    {
        $absence = Absence::findOrFail($id);

        $absence->update($data);

        return $absence;
    }

    public function delete($id)
    {
        $absence = Absence::findOrFail($id);

        return $absence->delete();
    }
}