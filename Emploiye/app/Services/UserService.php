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

        $role = Role::where('name', Role::EMPLOYEE)->firstOrFail();

        return User::create([
            'role_id'        => $data['role_id'] ?? $role->id,
            'firstname'      => $data['firstname'],
            'lastname'       => $data['lastname'],
            'cin'            => $data['cin'],
            'telephone'      => $data['telephone'] ?? null,
            'adresse'        => $data['adresse'] ?? null,
            'genre'          => $data['genre'],
            'date_naissance' => $data['date_naissance'] ?? null,
            'date_embauche'  => $data['date_embauche'] ?? null,
            'type_contrat'   => $data['type_contrat'],
            'salaire'        => $data['salaire'] ?? null,
            'photo'          => $photoPath,
            'email'          => $data['email'],
            'password'       => Hash::make($data['password']),
        ]);
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