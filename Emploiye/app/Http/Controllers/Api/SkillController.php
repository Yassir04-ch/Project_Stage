<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use App\Models\User;
use App\Services\SkillService;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    public function __construct(private SkillService $skillService) {}

    public function index()
    {
        return response()->json([
            'skills' => $this->skillService->getAll()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:skills,name'
        ]);

        $skill = $this->skillService->create($request->name);

        return response()->json([
            'message' => 'Compétence créée',
            'skill'   => $skill
        ], 201);
    }

    public function destroy(Skill $skill)
    {
        $this->skillService->delete($skill);
        return response()->json(['message' => 'Compétence supprimée']);
    }

    public function assignToEmployee(Request $request, User $user)
    {
        $request->validate([
            'skills'         => 'required|array',
            'skills.*.id'    => 'required|exists:skills,id',
            'skills.*.level' => 'required|in:beginner,intermediate,advanced',
        ]);

        $this->skillService->assignToEmployee($user, $request->skills);

        return response()->json([
            'message' => 'Compétences assignées',
            'skills'  => $this->skillService->getEmployeeSkills($user)
        ]);
    }

    public function getEmployeeSkills(User $user)
    {
        return response()->json([
            'skills' => $this->skillService->getEmployeeSkills($user)
        ]);
    }
}