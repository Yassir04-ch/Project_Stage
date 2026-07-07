<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::with(['chef', 'employees.role'])
            ->withCount('employees')
            ->latest()
            ->get();

        return response()->json(['services' => $services]);
    }

   public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:services,name',
            'description' => 'nullable|string',
            'chef_id'     => 'nullable|exists:users,id',
        ]);

        $service = Service::create([
            'name'        => $request->name,
            'description' => $request->description,
            'chef_id'     => $request->chef_id,
        ]);

        if ($request->filled('chef_id')) {

            $user = User::find($request->chef_id);
            if ($user) {
                $user->update([
                    'service_id' => $service->id,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Service créé avec succès.',
            'service' => $service->load(['chef', 'employees']),
        ], 201);
    }

    public function show(Service $service)
    {
        return response()->json([
            'service' => $service->load(['chef', 'employees.role', 'employees.skills'])
        ]);
    }

    public function update(Request $request, Service $service)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:services,name,' . $service->id,
            'description' => 'nullable|string',
            'chef_id'     => 'nullable|exists:users,id',
        ]);

        $service->update($request->only('name', 'description', 'chef_id'));

        return response()->json([
            'message' => 'Service mis à jour',
            'service' => $service->load(['chef', 'employees'])
        ]);
    }

    public function destroy(Service $service)
    {
        $service->employees()->update(['service_id' => null]);
        $service->delete();

        return response()->json(['message' => 'Service supprimé']);
    }

    public function assignEmployee(Request $request, Service $service)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        User::where('id', $request->user_id)
            ->update(['service_id' => $service->id]);

        return response()->json([
            'message' => 'Employé assigné au service',
            'service' => $service->load('employees.role')
        ]);
    }

    public function removeEmployee(Request $request, Service $service)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        User::where('id', $request->user_id)
            ->where('service_id', $service->id)
            ->update(['service_id' => null]);

        return response()->json([
            'message' => 'Employé retiré du service',
            'service' => $service->load('employees.role')
        ]);
    }
}