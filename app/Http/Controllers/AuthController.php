<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $this->authorize('create-delete-users');
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $data['password_confirmed'] = Hash::make($data['password_confirmed']);
        $userCreate = User::create($data);

        return response()->json($userCreate);
    }

    public function login(LoginRequest $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response(['errors' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();
        $user->tokens()->delete();
        $token = $user->createToken('token')->plainTextToken;

        return response()->json($token);
    }
}