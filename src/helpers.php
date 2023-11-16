<?php

use Illuminate\Http\JsonResponse;

if(!function_exists('apiResponse')) {
    /**
     * all api response structure
     * @param array $data
     * @param string $message
     * @param int $status
     * @return JsonResponse
     */
    function apiResponse(array $data,string $message = '',int $status = \Symfony\Component\HttpFoundation\Response::HTTP_OK): JsonResponse
    {
        if($message instanceof Exception) {
            if(app()->isProduction()) {
                $message = __('callmeaf::base.v1.unknown_error');
            } else {
                $message = $message->getMessage();
            }
        }
        return response()->json([
            'data' => $data,
            'message' => $message,
        ],$status);
    }
}

if(!function_exists('validationManager')) {
    /**
     * merge config validation with default validation
     * @param string $key
     * @param array $values
     * @param mixed $source
     * @return array
     */
    function validationManager(string $key,array $values,mixed $source): array
    {
        $validationKey = $source[$key] ?? null;
        if(is_array($validationKey)) {
            return $validationKey;
        }
        if($validationKey) {
            return [
                'required',
                ...$values,
            ];
        }
        return [
            'nullable',
            ...$values,
        ];
    }
}
