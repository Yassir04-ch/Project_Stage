<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AbsenceRequest;
use App\Services\AbsenceService;
use Illuminate\Http\Request;


class AbsenceController extends Controller
{
    private AbsenceService $absenceService;

    public function __construct()
    {
        $this->absenceService = new AbsenceService();
    }

    public function index()
    {
        $absences = $this->absenceService->getAll();
        return response()->json([
            "absences" =>$absences
        ],200);
    }

    public function store(Request $request)
    {

        $validated = $request->all(); 
        $absence = $this->absenceService->create($validated);
        // dd($absence);
        return response()->json([
            "message"=>"absence created",
            "absence"=>$absence
        ],201);
    }

    public function update(Request $request,$id)
    {
        $absence = $this->absenceService->update($id,$request->all());

        return response()->json( [
            "message"=>"absence Update",
            "absence"=>$absence
        ],201);
    }

    public function destroy($id)
    {
        $this->absenceService->delete($id);

        return response()->json([
            "message"=>"deleted"
        ]);
    }
}