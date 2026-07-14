<?php

namespace App\Services;

use App\Models\Application;
use App\Models\JobOffer;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

class ApplicationService
{
    public function apply(JobOffer $jobOffer, array $data, UploadedFile $cv)
    {
        if (!$jobOffer->is_open) {
            throw ValidationException::withMessages([
                'job_offer' => "Cette offre n'accepte plus de candidatures.",
            ]);
        }

        $alreadyApplied = Application::where('job_offer_id', $jobOffer->id)
            ->where('email', $data['email'])
            ->exists();

        if ($alreadyApplied) {
            throw ValidationException::withMessages([
                'email' => "Vous avez déjà postulé à cette offre avec cet email.",
            ]);
        }

        $path = $cv->store('cvs', 'public');

        $data['job_offer_id'] = $jobOffer->id;
        $data['cv_path'] = $path;
        $data['status'] = 'pending';

        return Application::create($data);
    }

    public function updateStatus(Application $application, string $status)
    {
        $application->update(['status' => $status]);

        return $application;
    }
}