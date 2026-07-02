<?php

namespace App\Services;

use App\Models\Justification;
use Illuminate\Support\Facades\Storage;
use App\Models\User;


class JustificationService
{

    public NotificationSevice $notify;

    public function __construct()
    {
        $this->notify = new NotificationSevice();
    }

    public function getAll()
    {
        return Justification::with(['absence','justifiedBy'])->get();
    }

    public function create(array $data)
    {
        $user = auth()->user();

        $data['status'] = 'pending';
        $data['justified_by'] = $user->id;

        if (isset($data['proof_file'])) {
            $data['proof_file'] = $data['proof_file']->store('justifications', 'public');
        }

        $justification = Justification::create($data);

        $admins = User::whereHas('role', function ($query) {
             $query->where('name', 'Administrateur');
           })->get();

        foreach ($admins as $admin) {
            $this->notify->notifyUser(
                $admin,
                'justification_created',
                'Nouvelle justification',
                "{$user->first_name} {$user->last_name} a soumis une justification.",
                [
                    'justification_id' => $justification->id,
                    'absence_id' => $justification->absence_id,
                    'employee_id' => $user->id,
                ]
            );
        }

        return $justification->load(['absence', 'justifiedBy']);
    }

    public function update(int $id, array $data)
    {
        $justification = Justification::findOrFail($id);

        if (isset($data['proof_file']) && $data['proof_file'] instanceof \Illuminate\Http\UploadedFile) {
            if ($justification->proof_file) {
                Storage::disk('public')->delete($justification->proof_file);
            }
            $data['proof_file'] = $data['proof_file']->store('justifications', 'public');
        }

        $justification->update($data);

        return $justification->load(['absence', 'justifiedBy']);
    }

    public function delete($id)
    {
        $justification = Justification::findOrFail($id);

        if ($justification->proof_file) {
            Storage::disk('public')
                ->delete($justification->proof_file);
        }

        return $justification->delete();
    }
}