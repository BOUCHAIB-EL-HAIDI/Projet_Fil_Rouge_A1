<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        return response()->json(Supplier::all());
    }

    public function store(Request $request)
    {
        if ($request->user()->role !== 'acheteur') {
            return response()->json(['message' => 'Seul l\'acheteur peut ajouter des fournisseurs.'], 403);
        }

        $request->validate([
            'name' => 'required|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $supplier = Supplier::create($request->all());
        return response()->json($supplier, 201);
    }

    public function show(Supplier $supplier)
    {
        return response()->json($supplier);
    }

    public function update(Request $request, Supplier $supplier)
    {
        if ($request->user()->role !== 'acheteur') {
            return response()->json(['message' => 'Seul l\'acheteur peut modifier des fournisseurs.'], 403);
        }

        $supplier->update($request->all());
        return response()->json($supplier);
    }

    public function destroy(Request $request, Supplier $supplier)
    {
        if ($request->user()->role !== 'acheteur') {
            return response()->json(['message' => 'Seul l\'acheteur peut supprimer des fournisseurs.'], 403);
        }

        $supplier->delete();
        return response()->json(null, 204);
    }
}
