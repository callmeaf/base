<?php

use Illuminate\Http\JsonResponse;

if(!function_exists('apiResponse')) {
    /**
     * @param array $data
     * @param string $message
     * @param int $status
     * @return JsonResponse
     */
    function apiResponse(array $data,string $message = '',int $status = \Symfony\Component\HttpFoundation\Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
        ],$status);
    }
}
