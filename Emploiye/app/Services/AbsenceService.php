<?php

namespace App\Services;
use Illuminate\Http\Request;

use App\Models\Absence;
use App\Models\User;

class AbsenceService
{

    public NotificationSevice $notify;

    public function __construct()
    {
        $this->notify = new NotificationSevice();
    }

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

        $absence = Absence::create($data);

        $employee = User::find($data['user_id']);

        if ($employee) {
            $this->notify->notifyUser(
                $employee,
                'absence_created',
                'Nouvelle absence',
                'Une absence a été enregistrée pour vous.',
                [
                    'absence_id' => $absence->id,
                    'date' => $absence->date,
                    'status' => $absence->status,
                ]
            );
        }

        return $absence;
    }
    
    public function getMyAbsences(int $userId)
    {
        return Absence::where('user_id', $userId)
            ->with('justification')
            ->latest('date')
            ->get();
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