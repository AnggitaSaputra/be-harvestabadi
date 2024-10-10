<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        try {
            $data = User::all();
            return new UserResource('success', 'Retrieved data successfully', $data);
        } catch (\Throwable $th) {
            return new UserResource('error', 'Retrieved data failed', $th->getMessage());
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return new UserResource('error', 'Validation failed', $validator->errors());
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            return new UserResource('success', 'User created successfully', $user);
        } catch (\Throwable $th) {
            return new UserResource('error', 'User creation failed', $th->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $user = User::findOrFail($id);
            return new UserResource('success', 'User retrieved successfully', $user);
        } catch (\Throwable $th) {
            return new UserResource('error', 'User retrieval failed', $th->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'email' => 'string|email|max:255|unique:users,email,' . $id,
            'password' => 'string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return new UserResource('error', 'Validation failed', $validator->errors());
        }

        try {
            $user = User::findOrFail($id);
            $user->update($request->only('name', 'email', 'password'));

            if ($request->has('password')) {
                $user->password = Hash::make($request->password);
                $user->save();
            }

            return new UserResource('success', 'User updated successfully', $user);
        } catch (\Throwable $th) {
            return new UserResource('error', 'User update failed', $th->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            return new UserResource('success', 'User deleted successfully', null);
        } catch (\Throwable $th) {
            return new UserResource('error', 'User deletion failed', $th->getMessage());
        }
    }
}
