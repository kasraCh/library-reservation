<?php

namespace App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    public function success(mixed $data = [], string $message = 'success', array $extra = [], int $statusCode = 200) : JsonResponse
    {
        return response()->json(array_merge(
            [
                'data' => $data,
                'message' => $message,
                'status_code' => $statusCode,
            ],$extra ));
    }
    public function failure(mixed $data = [], string $message = 'failure', array $extra = [], int $statusCode = 400) : JsonResponse
    {
        return response()->json(array_merge(
            [
                'data' => $data,
                'message' => $message,
                'status_code' => $statusCode,
            ],$extra ));
    }
}
