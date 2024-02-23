<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;


class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'me', 'refresh', 'logout']]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $credentials = request(['email', 'password']);
        $token = Auth::attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'error' => 'Unauthorized'
            ], 401);
        }

        $user = Auth::user();
        return response()->json([
            'status' => 'success',
            'token' => $token,
            'token_type' => 'bearer',
            'user' => $user
        ]);
    }

    public function me()
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => 'error',
                'error' => 'Unauthorized'
            ], 401);
        }
        return response()->json([
            'status' => 'success',
            'user' => Auth::user()
        ]);
    }

    public function refresh()
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => 'error',
                'error' => 'Unauthorized'
            ], 401);
        }
        $newToken = Auth::refresh();
        return response()->json([
            'status' => 'success',
            'token' => $newToken
        ]);
    }

    public function logout()
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => 'error',
                'error' => 'Unauthorized'
            ], 401);
        }
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Logged out Successfully'
        ]);
    }
}
