<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

use App\Http\Requests\RegisterRequest;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();
        $data['email'] = strtolower(trim($data['email']));
        
        $user = User::create($data);

        return response()->json([
            'message' => 'Utilisateur inscrit avec succès',
            'user' => $user->load('department'),
            'token' => $user->createToken('auth_token')->plainTextToken,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = [
            'email' => strtolower(trim($request->email)),
            'password' => $request->password,
        ];

        if (!Auth::attempt($credentials)) {
            $userExists = User::where('email', $credentials['email'])->exists();
            
            throw ValidationException::withMessages([
                'email' => [$userExists ? 'Mot de passe incorrect.' : 'Identifiants invalides.'],
            ]);
        }

        $user = Auth::user();

        return response()->json([
            'message' => 'Connexion réussie',
            'user' => $user->load('department'),
            'token' => $user->createToken('auth_token')->plainTextToken,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    public function me(Request $request)
    {
        return response()->json($request->user()->load('department'));
    }
}
