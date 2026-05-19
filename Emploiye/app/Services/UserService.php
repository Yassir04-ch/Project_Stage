<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserService
{
    public function createEmployee(array $data): User
    {
        $photoPath = null;
        if (isset($data['photo'])) {
            $photoPath = $data['photo']->store('photos/employees', 'public');
        }

        $role = Role::where('name','Employé')->firstOrFail();
        $data['role_id'] = $role->id;
        $data['password'] = Hash::make($data['password']);
        $data['photo'] = $photoPath;
        $user = User::create($data);
        return $user;
    }

    public function updateEmployee(User $user, array $data): User
    {
        if (isset($data['photo'])) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $data['photo'] = $data['photo']->store('photos/employees', 'public');
        }

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);
        return $user->fresh('role');
    }

    public function deleteEmployee(User $user): void
    {
        if ($user->photo) {
            Storage::disk('public')->delete($user->photo);
        }
        $user->delete();
    }

   
    public function getEmployees(array $filters = [])
    {
        return User::with('role', 'skills')
            ->whereHas('role', fn($q) => $q->where('name','Employee'))
            ->when($filters['search'] ?? null, function ($q, $search) {
                $q->where(function ($q) use ($search) {
                    $q->where('firstname', 'like', "%$search%")
                      ->orWhere('lastname',  'like', "%$search%")
                      ->orWhere('email',     'like', "%$search%")
                      ->orWhere('cin',       'like', "%$search%");
                });
            })
            ->when($filters['type_contrat'] ?? null, fn($q, $v) =>
                $q->where('type_contrat', $v)
            )
            ->when($filters['genre'] ?? null, fn($q, $v) =>
                $q->where('genre', $v)
            )
            ->paginate(15);
    }
}