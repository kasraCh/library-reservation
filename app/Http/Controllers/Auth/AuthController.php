<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\LoginRequest;
use App\Http\Requests\Users\RegisterRequest;
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
        $data = $request->validated();

        if ($user = User::create($data)) {
            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->success($user, 'User registered successfully.', ['token' => $token]);
        }

        return $this->failure();
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return $this->failure(null, 'Invalid credentials.');
        }
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success($user, 'User logged in successfully.', ['token' => $token]);
    }

    public function logout(Request $request): JsonResponse
    {
        if ($request->user()->currentAccessToken()->delete()) {
            return $this->success(null, 'User logged out successfully.');
        }

        return $this->failure(null, 'Something went wrong., Please try again later.');
    }
}
