<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ProfileResource;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function profile($email)
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return new ProfileResource('error', 'User not found!', null);
        }

        return new ProfileResource('success', 'User found.', $user);
    }

    public function updateProfile(Request $request, $email)
    {
        $user = User::where('email', $email)->first();

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'email' => 'email|max:255|unique:users,email,' . $user->id,
        ]);

        if ($validator->fails()) {
            return new ProfileResource('error', 'Validation Error', $validator->errors());
        }

        $user->update($request->all());

        return new ProfileResource('success', 'Profile updated successfully.', $user);
    }

    public function updatePassword(Request $request, $email)
    {
        $user = User::where('email', $email)->first();

        $validator = Validator::make($request->all(), [
            'current_password' => 'string',
            'new_password' => 'string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return new ProfileResource('error', 'Validation Error', $validator->errors());
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return new ProfileResource('error', 'Current password is incorrect.', null);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return new ProfileResource('success', 'Password updated successfully.', null);
    }
}
