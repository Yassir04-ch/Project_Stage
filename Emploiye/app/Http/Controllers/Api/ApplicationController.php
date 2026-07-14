<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApplicationRequest;
use App\Models\Application;
use App\Models\JobOffer;
use App\Services\ApplicationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    private ApplicationService $service;

    public function __construct()
    {
        $this->service = new ApplicationService();
    }

    public function store(ApplicationRequest $request, JobOffer $jobOffer): JsonResponse
    {
        $data = $request->validated();
        $cv = $request->file('cv');
        unset($data['cv']);

        $application = $this->service->apply($jobOffer, $data, $cv);

        return response()->json([
            'message' => 'Votre candidature a été envoyée avec succès.',
            'data'    => $application,
        ], 201);
    }

    public function indexByOffer(JobOffer $jobOffer)
    {
        return response()->json([
            'message' => 'applications for offer',
            'data'    => $jobOffer->applications()->latest()->get(),
        ], 200);
    }

    public function updateStatus(Request $request, Application $application): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:pending,accepted,rejected',
        ]);

        $application = $this->service->updateStatus($application, $request->status);

        return response()->json([
            'message' => 'Statut de la candidature mis à jour',
            'data'    => $application,
        ], 200);
    }

    public function destroy(Application $application): JsonResponse
    {
        $application->delete();

        return response()->json(['message' => 'Candidature supprimée avec succès']);
    }
}