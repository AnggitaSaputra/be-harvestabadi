<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AuthResource;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    // Method untuk mendapatkan user dari token
    public function user(Request $request)
    {
        $authHeader = $request->header('Authorization');
        $token = str_replace('Bearer ', '', $authHeader);
        $tokenInstance = PersonalAccessToken::findToken($token);

        if ($tokenInstance) {
            return new AuthResource(true, 'User authenticated', $tokenInstance->tokenable); // Mengembalikan user yang terhubung dengan token
        } else {
            return new AuthResource(false, 'Invalid token', null);
        }
    }

    // Method untuk login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'remember' => 'boolean',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->filled('remember');

        $user = User::where('email', $credentials['email'])->first();

        if ($user && Auth::attempt($credentials, $remember)) {
            $token = $user->createToken($request->email)->plainTextToken;
            $data = [
                'token' => $token,
                'name' => $user->name, // Menggunakan nama user
            ];
            return new AuthResource(true, 'Login successful.', $data);
        }

        return new AuthResource(false, 'Invalid credentials.', null);
    }

    // Method untuk logout
    public function logout(Request $request)
    {
        $authHeader = $request->header('Authorization');
        $token = str_replace('Bearer ', '', $authHeader);
        $tokenInstance = PersonalAccessToken::findToken($token);

        if ($tokenInstance) {
            $tokenInstance->delete();
            return new AuthResource(true, 'Berhasil logout!', null);
        } else {
            return new AuthResource(false, 'Token tidak ditemukan.', null);
        }
    }
    public function verifyToken(Request $request)
    {
        $authHeader = $request->header('Authorization');
        $token = str_replace('Bearer ', '', $authHeader);
        $tokenInstance = \Laravel\Sanctum\PersonalAccessToken::findToken($token);

        if ($tokenInstance) {
            return new AuthResource(true, 'User authenticated', $tokenInstance->tokenable);
        }

        return new AuthResource(false, 'Invalid token', null);
    }

}
