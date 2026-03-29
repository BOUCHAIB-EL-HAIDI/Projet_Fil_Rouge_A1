<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index()
    {
        return User::with('department')->orderBy('name')->get();
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|string|in:demandeur,manager,directeur,acheteur',
            'department_id' => 'required_if:role,manager|exists:departments,id',
        ]);

        if ($request->role === 'directeur') {
            $existingDirecteur = User::where('role', 'directeur')->where('id', '!=', $user->id)->first();
            if ($existingDirecteur) {
                return response()->json([
                    'message' => 'Un seul Directeur est autorisé dans le système. Veuillez d\'abord changer le rôle du directeur actuel.'
                ], 422);
            }
        }

        if ($request->role === 'manager') {
            $dept = \App\Models\Department::find($request->department_id);
            if ($dept->manager_id && $dept->manager_id !== $user->id) {
                $oldManager = User::find($dept->manager_id);
                if ($oldManager) {
                    $oldManager->update(['role' => 'demandeur']);
                }
            }
            
            $dept->update(['manager_id' => $user->id]);
            $user->update([
                'role' => 'manager',
                'department_id' => $request->department_id
            ]);
        } else {
            if ($user->role === 'manager') {
                 $user->managedDepartment()->update(['manager_id' => null]);
            }
            $user->update(['role' => $request->role]);
            if ($request->has('department_id')) {
                $user->update(['department_id' => $request->department_id]);
            }
        }

        return $user->load('department');
    }
}
