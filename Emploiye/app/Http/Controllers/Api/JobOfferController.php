<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobOfferRequest;
use App\Models\JobOffer;
use App\Services\JobOfferService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobOfferController extends Controller
{
    private JobOfferService $service;

    public function __construct()
    {
        $this->service = new JobOfferService();
    }

    public function index(Request $request)
    {
        $query = JobOffer::latest();

        if (!$request->boolean('all')) {
            $query->where('status', 'open')
                  ->where(function ($q) {
                      $q->whereNull('deadline')->orWhereDate('deadline', '>=', now());
                  });
        }

        return response()->json([
            'message' => 'job offers',
            'data'    => $query->get(),
        ], 200);
    }

    public function show($id)
    {
        $jobOffer = $this->service->getById($id);

        return response()->json([
            'message' => 'job offer details',
            'data'    => $jobOffer,
        ], 200);
    }

    public function store(JobOfferRequest $request): JsonResponse
    {
        $jobOffer = $this->service->create($request->validated(), $request->user()?->id);

        return response()->json([
            'message' => 'Offre créée avec succès',
            'data'    => $jobOffer,
        ], 201);
    }

    public function update(JobOfferRequest $request, JobOffer $jobOffer): JsonResponse
    {
        $jobOffer = $this->service->update($jobOffer, $request->validated());

        return response()->json([
            'message' => 'Offre mise à jour avec succès',
            'data'    => $jobOffer,
        ], 200);
    }

    public function destroy(JobOffer $jobOffer): JsonResponse
    {
        $jobOffer->delete();

        return response()->json(['message' => 'Offre supprimée avec succès']);
    }
}