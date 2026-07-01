<?php

namespace App\Services;

use App\Models\Justification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;


class JustificationService
{
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
            $data['proof_file'] = $data['proof_file']
                ->store('justifications', 'public');
        }

        return Justification::create($data)
            ->load(['absence', 'justifiedBy']);
    }

    public function update($id, array $data)
    {
        $justification = Justification::findOrFail($id);

        if (isset($data['proof_file'])) {

            if ($justification->proof_file) {
                Storage::disk('public')
                    ->delete($justification->proof_file);
            }

            $data['proof_file'] = $data['proof_file']
                ->store('justifications', 'public');
        }

        $justification->update($data);

        return $justification->load(['absence','justifiedBy']);
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