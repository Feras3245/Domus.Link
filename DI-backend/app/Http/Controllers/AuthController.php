<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request) {
        $fields = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed'
        ]);
        $user = new User();
        $user->name = $fields['name'];
        $user->email = $fields['email'];
        $user->password = bcrypt($fields['password']);
        $user->role = 'USER';
        $user->save();
        $token = $user->createToken($request->email);
        return [
            'user' => new UserResource($user),
            'token' => $token->plainTextToken
        ];
    }

    public function login(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            abort(401, 'The provided credentials are incorrect.');
        }

        $token = $user->createToken($request->email);
        return [
            'user' => new UserResource($user),
            'token' => $token->plainTextToken,
        ];
    }

    public function logout(Request $request) {
        $request->user()->tokens()->delete();
        return [
            'message' => 'You are logged out.'
        ];
    }
}
