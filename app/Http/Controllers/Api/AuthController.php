<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        // Get validated data
        $validated = $request->validated();

        // Create the user
        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Create a personal access token for the new user
        $token = $user->createToken('auth_token')->accessToken;

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'access_token' => $token,
        ], 201);
    }

    /**
     * Login user and create token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->first();

        // Check if user exists
        if (!$user) {
            return response()->json([
                'message' => 'No account found with this email address.',
                'errors' => ['email' => ['The provided email is not registered.']]
            ], 401);
        }

        // Verify password
        if (!Hash::check($validated['password'], $user->password)) {
            // You might want to track failed login attempts here
            return response()->json([
                'message' => 'Invalid credentials.',
                'errors' => ['password' => ['The provided password is incorrect.']]
            ], 401);
        }

        // Check if email is verified (if you have email verification enabled)
        if (config('auth.verify_email') && !$user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Please verify your email address.',
                'errors' => ['email' => ['Email address is not verified.']]
            ], 403);
        }

        // Create token
        $token = $user->createToken('auth_token')->accessToken;

        // Update last login timestamp
        $user->update(['last_login_at' => now()]);

        return response()->json([
            'message' => 'User logged in successfully',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Logout user (Revoke the token).
     */
    public function logout(): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }

    /**
     * Get the authenticated user.
     */
    public function user(): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return response()->json($user);
    }
}
