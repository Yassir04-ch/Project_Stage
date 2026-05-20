<?php
namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends Controller {
    public function users(){
        $users = User::with('role')->get();
        return response()->json([
            'success' => true,
            'users' => $users,
        ],200);
    }

    public function stats(): JsonResponse
    {

       $total_users = User::count();
       $admins = User::where('role_id',1)->count();
       $employees = User::where('role_id',4)->count();
       $managers = User::where('role_id',2)->count();
       $rhs = User::where('role_id',3)->count();
        return response()->json([
            'success' => true,
            'data' => [
                'total_users' =>$total_users,
                'admins' =>$admins,
                'employees' => $employees,
                'managers' => $managers,
                'rhs' => $rhs,
            ]
        ], 200);
    }

}