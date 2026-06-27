<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AbsenceRequest;
use App\Services\AbsenceService;
use Illuminate\Http\JsonResponse;

class AbsenceController extends Controller
{
    private AbsenceService $absenceService;

    // Dependency Injection f l-constructor direct
    public function __construct(AbsenceService $absenceService)
    {
        $this->absenceService = $absenceService;
    }

    public function index(): JsonResponse
    {
        $absences = $this->absenceService->getAll();
        return response()->json([
            "absences" => $absences
        ], 200);
    }

    public function store(AbsenceRequest $request): JsonResponse
    {
        $validated = $request->validated(); 
        $absence = $this->absenceService->create($validated);

        return response()->json([
            "message" => "Absence enregistrée avec succès",
            "absence" => $absence
        ], 201);
    }

    public function update(AbsenceRequest $request, $id): JsonResponse
    {
        $validated = $request->validated();
        $absence = $this->absenceService->update($id, $validated);

        return response()->json([
            "message" => "Absence mise à jour avec succès",
            "absence" => $absence
        ], 200);
    }

    public function destroy($id): JsonResponse
    {
        $this->absenceService->delete($id);

        return response()->json([
            "message" => "Enregistrement supprimé avec succès"
        ], 200);
    }
}