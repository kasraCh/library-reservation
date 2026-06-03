<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\LoginRequest;
use App\Http\Requests\Users\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponse\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponse;

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create($request->validated());

        return $this->success(new UserResource($user), 'User registered successfully.');
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::where('email', $data['email'])->first();

        $password = $request->string('password')->toString();

        if (! $user || ! Hash::check($password, $user->password)) {
            return $this->failure(null, 'Invalid credentials.');
        }
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success(new UserResource($user), 'User logged in successfully.', ['token' => $token]);
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return $this->failure(null, 'Something went wrong., Please try again later.');
        }

        $user->currentAccessToken()->delete();

        return $this->success(null, 'User logged out successfully.');

    }
}
