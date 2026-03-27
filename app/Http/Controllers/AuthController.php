<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create($validatedData);
        
        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'User registered successfully', 
            'user' => $user, 
            'token' => $token
        
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'user' => auth()->user(),
            'message' => 'Login successful', 
            'token' => $token
        
        ], 200);
    }

    public function logout()
    {
        auth()->logout();
        return response()->json([ 'message' => 'Successfully logged out' ], 200);
    }

    public function me()
    {
        return response()->json(auth()->user());
    }

    public function __invoke(Request $request)
    {
        //
    }
}
