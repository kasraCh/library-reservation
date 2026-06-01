<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class ApiController extends Controller
{
//    use ApiResponse;
    public function successResponse($data=null, string $message='success', int $status=200): JsonResponse
    {
        return Response()->json(
            [
                'data' => $data,
                'message' => $message,
                'status' => $status
            ],
        $status);
    }

    public function errorResponse($data=null, $message='error', int $status=400): JsonResponse
    {
        return Response()->json([
//            'data' => $data,
            'message' => $message,
            'status' => $status
        ], $status);
    }
}
