<?php

namespace App\Traits;

trait APIResponse
{
    /**
     * Success response
     *
     * @param mixed $data
     * @param string|null $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function successResponse($data = null, $message = null)
    {
        return response()->json([
            'data' => $data,
            'message' => $message ?? 'Operation successful',
            'success' => true,
        ]);
    }

    /**
     * Error response
     *
     * @param string|null $message
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorResponse($message = 'Operation failed', $data = null, $statusCode = 400)
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
            'success' => false,
        ], $statusCode);
    }
}
