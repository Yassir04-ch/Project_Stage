<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AbsenceRequest;
use App\Models\Absence;
use Illuminate\Http\Request;
use App\Services\AbsenceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\QueryException;


class AbsenceController extends Controller
{
    private AbsenceService $absenceService;

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

        $exists = Absence::where('user_id', $validated['user_id'])
            ->whereDate('date', $validated['date'])
            ->exists();

        if ($exists) {
            return response()->json([
                "message" => "Une absence existe déjà pour cet employé à cette date."
            ], 422);
        }

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

        public function myAbsences(Request $request): JsonResponse
    {
        $absences = $this->absenceService->getMyAbsences($request->user()->id);

        return response()->json([
            'absences' => $absences
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