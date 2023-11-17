<?php

use Illuminate\Http\JsonResponse;

if(!function_exists('apiResponse')) {
    /**
     * all api response structure
     * @param array|null $data
     * @param string|null $message
     * @param int|null $status
     * @return JsonResponse
     */
    function apiResponse(null|array $data,?string $message = '',?int $status = null): JsonResponse
    {
        $status = $status ?? \Symfony\Component\HttpFoundation\Response::HTTP_OK;
        if(app()->isProduction() && $status >= 500) {
            $message = __('callmeaf::base-v1.unknown_error');
        }

        $transformedData = [];
        if($errors = @$data['errors']) {
            $transformedData['errors'] = $errors;
        } else {
            $transformedData['data'] = $data;
        }
        $transformedData['message'] = $message ?? '';

        return response()->json($transformedData,$status);
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
