<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;

class DepartmentController extends Controller
{
    public function index()
    {
        return Department::with('manager')->orderBy('name')->get();
    }

    public function store(StoreDepartmentRequest $request)
    {
        $department = Department::create($request->validated());
        return response()->json($department->load('manager'), 201);
    }

    public function update(UpdateDepartmentRequest $request, Department $department)
    {
        $department->update($request->validated());
        return $department->load('manager');
    }

    public function destroy(Department $department)
    {
        if (auth()->user()->role !== 'directeur') {
            abort(403, 'Seul le directeur peut supprimer des départements.');
        }

        if ($department->members()->exists()) {
            return response()->json([
                'message' => 'Impossible de supprimer un département qui contient encore des membres.',
            ], 422);
        }

        $department->delete();
        return response()->json(null, 204);
    }
}
