<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\JustificationRequest;
use App\Services\JustificationService;
use Illuminate\Http\Request;
use App\Models\Justification;

class JustificationController extends Controller
{
    private JustificationService $justificationService;

    public function __construct()
    {
        $this->justificationService = new JustificationService();
    }

    
    public function index()
    {
        $justifications = $this->justificationService->getAll();

        return response()->json([
            "justifications" => $justifications
        ],200);
    }

    public function getByAbsence($absenceId)
    {
        $justifications = Justification::with(['absence', 'justifiedBy'])
            ->where('absence_id', $absenceId)
            ->get();

        return response()->json([
            "justifications" => $justifications
        ]);
    }


    public function store(JustificationRequest $request)
    {
        $justification = $this->justificationService->create($request->validated());

        return response()->json([
            "message" => "Justification created",
            "justification" => $justification
        ], 201);
    }

    
    public function update(JustificationRequest $request, int $id)
    {
        $data = $request->validated();

        if ($request->hasFile('proof_file')) {
            $data['proof_file'] = $request->file('proof_file');
        }

        $justification = $this->justificationService->update($id, $data);

        return response()->json([
            "message"       => "Justification updated",
            "justification" => $justification
        ], 200);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);

        $justification = Justification::with('absence')->findOrFail($id);

        $justification->status = $request->status;
        $justification->save();

        if ($request->status === 'approved') {
            $justification->absence->update([
                'is_justified' => true,
            ]);
        }

        if ($request->status === 'rejected') {
            $justification->absence->update([
                'is_justified' => false,
            ]);
        }

        return response()->json([
            'message' => 'Status updated successfully',
            'justification' => $justification->fresh()->load('absence'),
        ]);
    }

    
    public function destroy(int $id)
    {

        $this->justificationService->delete($id);

        return response()->json([
            "message" => "Justification deleted"
        ],200);
    }


}