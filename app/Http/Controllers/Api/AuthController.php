<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * @group Authentication
 *
 * Endpoints for registering, logging in, and logging  out.
 */
class AuthController extends Controller
{
    /**
     * Register a new user
     *
     * Create a new account and receive an API token.
     *
     * @unauthenticated
     *
     * @bodyParam name string required The user's display name. Example: John Doe
     * @bodyParam email string required The user's email address. Example: john@example.com
     * @bodyParam password string required The user's password (min 8 characters). Example: secret123
     * @bodyParam password_confirmation string required Must match password. Example: secret123
     *
     * @response status=201 scenario="success" {
     *   "user": { "id": 1, "name": "John Doe", "email": "john@example.com" },
     *   "token": "1|abc123def456..."
     * }
     * @response status=422 scenario="validation error" {
     *   "message": "The email field is required.",
     *   "errors": { "email": ["The email field is required."] }
     * }
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
        ], 201);
    }

    /**
     * Login
     *
     * Authenticate with existing credentials and receive an API token.
     *
     * @unauthenticated
     *
     * @bodyParam email string required The user's email. Example: john@example.com
     * @bodyParam password string required The user's password. Example: secret123
     *
     * @response status=200 scenario="success" {
     *   "user": { "id": 1, "name": "John Doe", "email": "john@example.com" },
     *   "token": "1|abc123def456..."
     * }
     * @response status=401 scenario="invalid credentials" {
     *   "message": "Invalid credentials"
     * }
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }

    /**
     * Logout
     *
     * Revoke the current API token.
     *
     * @authenticated
     *
     * @response status=200 scenario="success" {
     *   "message": "Logged out"
     * }
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }
}
