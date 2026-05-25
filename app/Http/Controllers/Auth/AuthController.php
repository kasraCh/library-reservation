<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\LoginRequest;
use App\Http\Requests\Users\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        if ($user = User::create($data)) {
            $token = $user->createToken('auth_token')->plainTextToken;
            return new UserResource($user)
                ->additional(['token' => $token, 'message' => 'user created']);
        } else {
            return response()->json([
                'data' => [
                    'status' => 'error',
                    'message' => 'cant create user'
                ], 400
            ]);
        }
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json([
                'data' => [
                    'status' => 'error',
                    'message' => 'not found user with this email or password'
                ], 401
            ]);
        }
        $token = $user->createToken('auth_token')->plainTextToken;

        return new UserResource($user)
            ->additional(['token' => $token, 'message' => 'user logged']);
    }

    public function logout(Request $request)
    {
        if ($request->user()->currentAccessToken()->delete()) {
            return response()->json([
                'data' => [
                    'status' => 'success',
                    'message' => 'logout success'
                ], 200
            ]);
        }

        return response()->json([
            'data' => [
                'status' => 'error',
                'message' => 'logout error'
            ], 400
        ]);
    }
}
