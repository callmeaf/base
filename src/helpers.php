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
    function apiResponse(null|array $data,null|Exception|string $message = '',?int $status = null): JsonResponse
    {
        $status = $status ?? \Symfony\Component\HttpFoundation\Response::HTTP_OK;
        if($message instanceof Exception) {
            $message = $message->getMessage();
        }
        if(app()->isProduction() && $status >= 500) {
            $message = __('callmeaf-base::v1.unknown_error');
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


if (!function_exists('stringReplacer')) {
    /**
     * @param string|null $subject
     * @param array $replace
     * @return string|int|null
     */
    function stringReplacer(null|string $subject, array $replace = []): null|string|int
    {
        if (is_null($subject)) {
            return null;
        }
        if (empty($replace)) {
            return $subject;
        }
        return str_replace(
            array_keys($replace),
            array_values($replace),
            $subject,
        );
    }
}

if(!function_exists('enumTranslator')) {
    /**
     * Translate each enum case
     * @param $enumCase
     * @param array $languages
     * @param string|int|null $defaultValue
     * @param array $replace
     * @return string|int|null
     */
    function enumTranslator($enumCase, array $languages, null|string|int $defaultValue = null, array $replace = []): null|string|int
    {
        if (empty($enumCase)) {
            return null;
        }
        if (!empty($replace)) {
            $replace[':'] = '';
        }
        if (is_string($enumCase)) {
            return stringReplacer(
                $languages[$enumCase] ?? $defaultValue,
                $replace,
            );
        }
        $source = $languages[get_class($enumCase)] ?? [];
        return stringReplacer($source[$enumCase->name] ?? $defaultValue, $replace);
    }
}
