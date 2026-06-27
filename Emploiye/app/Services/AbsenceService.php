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
        if ($data['status'] === 'absent') {
            $data['check_in'] = null;
            $data['check_out'] = null;
        }

        return Absence::create($data);
    }

    public function update($id, array $data)
    {
        $absence = Absence::findOrFail($id);

        if (isset($data['status']) && $data['status'] === 'absent') {
            $data['check_in'] = null;
            $data['check_out'] = null;
        }

        $absence->update($data);

        return $absence;
    }

    public function delete($id)
    {
        $absence = Absence::findOrFail($id);
        return $absence->delete();
    }
}