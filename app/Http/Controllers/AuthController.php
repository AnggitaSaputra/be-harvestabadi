<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AuthResource;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function user(Request $request)
    {
        $authHeader = $request->header('Authorization');
        $token = str_replace('Bearer ', '', $authHeader);
        $tokenInstance = PersonalAccessToken::findToken($token);

        if ($tokenInstance) {
            return new AuthResource('success', 'User authenticated', $tokenInstance->tokenable); // Mengembalikan user yang terhubung dengan token
        } else {
            return new AuthResource('error', 'Invalid token', null);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'remember' => 'boolean',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            $token = $user->createToken('authToken')->plainTextToken;
            $data = [
                'token' => $token,
                'name' => $user->name,
            ];
            return new AuthResource('success', 'Login successful.', $data);
        }

        return new AuthResource('error', 'Invalid credentials.', null);
    }


    // Method untuk logout
    public function logout(Request $request)
    {
        $authHeader = $request->header('Authorization');
        $token = str_replace('Bearer ', '', $authHeader);
        $tokenInstance = \Laravel\Sanctum\PersonalAccessToken::findToken($token);

        if ($tokenInstance) {
            $tokenInstance->delete();
            return new AuthResource('success', 'Berhasil logout!', null);
        } else {
            return new AuthResource('error', 'Token tidak ditemukan.', null);
        }
    }

    public function verifyToken(Request $request)
    {
        $authHeader = $request->header('Authorization');
        $token = str_replace('Bearer ', '', $authHeader);
        $tokenInstance = \Laravel\Sanctum\PersonalAccessToken::findToken($token);

        if ($tokenInstance) {
            return new AuthResource('success', 'User authenticated', $tokenInstance->tokenable);
        }

        return new AuthResource('error', 'Invalid token', null);
    }

}
