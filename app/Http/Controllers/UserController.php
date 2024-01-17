<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function Login(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = User::query()->where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                "success" => false,
                "message" => ['Username or password incorrect']
            ]);
        }

        $user->tokens()->delete();

        return response()->json([
            "success" => true,
            'token' => $user->createToken('auth_token')->plainTextToken
        ]);
    }

    public function Register(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users|max:255',
            'password' => 'required|string|min:8',
        ]);
        try {
            $user = new User();
            $user->name = $data['name'];
            $user->password = Hash::make($data['password']);
            $user->email = $data['email'];
            $user->save();
        } catch (QueryException) {
            return response()->json(['message' => 'Registration failed', "success" => false]);
        }
        return response()->json(['message' => 'User registered successfully', "success" => true]);
    }
}
