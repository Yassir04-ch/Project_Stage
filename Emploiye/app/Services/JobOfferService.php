<?php

namespace App\Services;

use App\Models\JobOffer;
use Illuminate\Validation\ValidationException;

class JobOfferService
{
    public function create(array $data, ?int $userId = null)
    {
        if (!empty($data['salary_min']) && !empty($data['salary_max']) && $data['salary_min'] > $data['salary_max']) {
            throw ValidationException::withMessages([
                'salary_max' => 'Le salaire maximum doit être supérieur au salaire minimum',
            ]);
        }

        $data['status'] = $data['status'] ?? 'open';
        $data['created_by'] = $userId;

        return JobOffer::create($data);
    }

    public function update(JobOffer $jobOffer, array $data)
    {
        if (!empty($data['salary_min']) && !empty($data['salary_max']) && $data['salary_min'] > $data['salary_max']) {
            throw ValidationException::withMessages([
                'salary_max' => 'Le salaire maximum doit être supérieur au salaire minimum',
            ]);
        }

        $jobOffer->update($data);

        return $jobOffer;
    }

    public function getById($id)
    {
        return JobOffer::findOrFail($id);
    }
}