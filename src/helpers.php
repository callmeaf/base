<?php

use Illuminate\Http\JsonResponse;

if(!function_exists('apiResponse')) {
    /**
     * all api response structure
     * @param array $data
     * @param Exception|string|null $message
     * @param int|null $status
     * @return JsonResponse
     */
    function apiResponse(array $data = [],null|Exception|string $message = '',?int $status = null): JsonResponse
    {
        $status = $status ?? \Symfony\Component\HttpFoundation\Response::HTTP_OK;
        if($message instanceof Exception) {
            $status = $message->getCode();
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

        return response()->json($transformedData,$status ?: \Symfony\Component\HttpFoundation\Response::HTTP_EXPECTATION_FAILED);
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

if(!function_exists('toArrayResource'))
{
    /**
     * Improve performance of api resource by set each item as function and only call them if needed
     * @param array $data
     * @param array|int $only
     * @return array
     */
    function toArrayResource(array $data,array|int $only = []): array
    {
        $data = collect($data);
        if(@$only[0] !== '*' && !is_int($only)) {
            $only = collect($only)->reject(fn($value,$key) => str($key)->startsWith('!'));
            $data = $data->only($only);
        }
        return $data->map(fn($item) => $item())->toArray();
    }
}

if(!function_exists('setArrayKeys')) {
    function setArrayKeys(array $keys,array $values): array
    {
        $data = [];
        foreach ($keys as $index => $key) {
            $data[$key] = $values[$index];
        }
        return $data;
    }
}

if(!function_exists('randomDigits')) {
    function randomDigits(int $length): string
    {
        $result = '';

        for ($i = 0; $i < $length; $i++) {
            $result .= random_int(0, 9);
        }

        return $result;
    }
}

if(!function_exists('isApiRequest'))
{
    function isApiRequest(?\Illuminate\Http\Request $request = null): bool
    {
        $request = $request ?? request();
        return $request->is(config('callmeaf-base.api.prefix_url') . '/*');
    }
}
