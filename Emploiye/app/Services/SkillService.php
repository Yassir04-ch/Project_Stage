<?php

namespace App\Services;

use App\Models\Skill;
use App\Models\User;

class SkillService
{
    public function getAll()
    {
        return Skill::all();
    }

    public function create(string $name): Skill
    {
        return Skill::create(['name' => $name]);
    }

    public function delete(Skill $skill): void
    {
        $skill->delete();
    }

    public function assignToEmployee(User $user, array $skills): void
    {
        $sync = [];
        foreach ($skills as $skill) {
            $sync[$skill['id']] = ['level' => $skill['level']];
        }
        $user->skills()->sync($sync);
    }

    public function getEmployeeSkills(User $user)
    {
        return $user->skills()->withPivot('level')->get();
    }
}