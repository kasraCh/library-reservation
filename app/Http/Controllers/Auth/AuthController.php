<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Users\LoginRequest;
use App\Http\Requests\Users\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends ApiController
{
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        if ($user = User::create($data)) {
            $token = $user->createToken('auth_token')->plainTextToken;
//            return new UserResource($user)
//                ->additional(['token' => $token, 'message' => 'user created']);
            return $this->successResponse(new UserResource($user)->additional(['token' => $token]), 'User registered successfully');
        } else {
            return $this->errorResponse('User already exists', 409);
        }
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return $this->errorResponse(null, 'not found user with this email or passwor');
//            return response()->json([
//                'data' => [
//                    'status' => 'error',
//                    'message' => 'not found user with this email or password'
//                ], 401
//            ]
//            );
        }
        $token = $user->createToken('auth_token')->plainTextToken;

//        return new UserResource($user)
//            ->additional(['token' => $token, 'message' => 'user logged']);
        return $this->successResponse(new UserResource($user)->additional(['token' => $token]), 'User logged in successfully');

    }

    public function logout(Request $request)
    {
        if ($request->user()->currentAccessToken()->delete()) {
            return $this->successResponse(null, 'User logged out successfully');
//            return response()->json([
//                'data' => [
//                    'status' => 'success',
//                    'message' => 'logout success'
//                ], 200
//            ]);
        }

        return $this->errorResponse('somethings went wrong try again!', 401);

//        return response()->json([
//            'data' => [
//                'status' => 'error',
//                'message' => 'logout error'
//            ], 400
//        ]);
    }
}
