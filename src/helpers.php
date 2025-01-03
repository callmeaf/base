<?php

use Illuminate\Http\JsonResponse;

if(!function_exists('apiResponse')) {
    /**
     * all api response structure
     * @param array $data
     * @param Exception|string|null $message
     * @param int|null $status
     * @return JsonResponse
     *
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
     * @param array $rules
     * @param array $filters
     * @return array
     */
    function validationManager(array $rules,array $filters): array
    {
        $rules = collect($rules)->intersectByKeys($filters);
       foreach ($filters as $key => $values) {
           match (true) {
              is_array($values) => $rules[$key] = $values,
              $values === true => $rules[$key] = ['required',...$rules[$key]],
              $values === false => $rules[$key] = ['nullable',...$rules[$key]],
              default => $rules[$key],
           };
       }
       return $rules->toArray();
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

if(!function_exists('enumAsOptions')) {
    /**
     * Enum transformer to array for use in select options in front
     * @param array $cases
     * @param array|string $languages can be simple array or eloquent class
     * @param bool $asString
     * @return array
     */
    function enumAsOptions(array $cases, array|string $languages, bool $asString = false): array
    {
        if(is_string($languages)) {
            /**
             * @var \Callmeaf\Base\Contracts\HasEnum $languages
             */
            $languages = app($languages);
            $languages = $languages->enumsLang();
        }
        $options = [];
        foreach ($cases as $case)
        {
            $options[] = [
                'label' => enumTranslator($asString ? $case->value : $case, $languages),
                'value' => $case->value,
            ];
        }
        return $options;
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
        $apiPrefixUrl = str(config('callmeaf-base.api.prefix_url'))->replace('{locale}','*')->append('/*')->toString();
        return $request->is($apiPrefixUrl);
    }
}

if(!function_exists('searcherLikeValue')) {
    function searcherLikeValue(string|int|array $value,?string $likeSymbol = null): string
    {
        $likeSymbol = $likeSymbol ?? config('callmeaf-base.searcher_like_symbol');
        return $likeSymbol === '%' ? "$value%" : "%$value%";
    }
}

if (!function_exists('userCan')) {
    /**
     * @param \Callmeaf\Permission\Enums\PermissionName $permissionName
     * @param \Illuminate\Http\Request|null $request
     * @return bool
     */
    function userCan(\Callmeaf\Permission\Enums\PermissionName $permissionName, ?\Illuminate\Http\Request $request = null): bool
    {
        $request = $request ?? request();
        return !!$request->user()?->can($permissionName->value);
    }
}

if(!function_exists('authUser')) {
    /**
     * @param \Illuminate\Http\Request|null $request
     * @return \Callmeaf\User\Models\User|null
     */
    function authUser(?\Illuminate\Http\Request $request = null): ?\Callmeaf\User\Models\User
    {
        $request = $request ?? request();
        return $request->user();
    }
}

if(!function_exists('authId')) {
    /**
     * @return string|int|null
     */
    function authId(): string|int|null
    {
        return \Illuminate\Support\Facades\Auth::id();
    }
}

if(!function_exists('getTableName')) {
    /**
     * Get table name model
     * @param string $model
     * @return string
     */
    function getTableName(string $model): string
    {
        return app($model)->getTable();
    }
}

if (!function_exists('randomId')) {
    /**
     * @param int|null $length
     * @param string|null $prefix
     * @return string|null
     * @throws Exception
     */
    function randomId(?int $length, ?string $prefix = ''): ?string
    {
        if(is_null($length) || is_null($prefix)) {
            return null;
        }
        $result = '';

        for ($i = 0; $i < $length; $i++) {
            $result .= random_int(0, 9);
        }

        return $prefix . $result;
    }
}

if(!function_exists('currencyFormat')) {
    /**
     * @param int|float|null $value
     * @param bool $withCurrency
     * @param string|null $locale
     * @return string|null
     */
    function currencyFormat(int|null|float $value,bool $withCurrency = true,?string $locale = null): ?string
    {
        if(is_null($value)) {
            return null;
        }
        $value = number_format($value);
        if($withCurrency) {
            $value .= ' ' . __('callmeaf-base::v1.$',locale: $locale);
        }
        return $value;
    }
}

if(!function_exists('localScope')) {
    /**
     * @param string|null $locale
     * @return callable
     */
    function localScope(?string $locale = null): callable
    {
        return function(\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query) use ($locale) {
            $query->where(column: 'locale',operator: $locale ?? \Illuminate\Support\Facades\App::currentLocale());
        };
    }
}

if(!function_exists('publishedAndExpiredValidationRules')) {
    function publishedAndExpiredValidationRules(null|string|\Callmeaf\Base\Enums\DateTimeFormat $dateFormat = null)
    {
        if(is_null($dateFormat)) {
            $dateFormat = \Callmeaf\Base\Enums\DateTimeFormat::DATE_TIME_WITH_DASH_AND_TIME_WITH_DOUBLE_POINT;
        }
        if($dateFormat instanceof \Callmeaf\Base\Enums\DateTimeFormat) {
            $dateFormat = $dateFormat->value;
        }
        return [
            'published_at' => ['date_format:' . $dateFormat],
            'expired_at' => ['date_format:' . $dateFormat],
        ];
    }
}

if(!function_exists('isUsingClass')) {
    function isUsingClass(string $sourceClass,string $targetClass): bool
    {
        return in_array(needle: $targetClass,haystack: class_uses_recursive($sourceClass),strict: true);
    }
}

if(!function_exists('isUsingSoftDelete')) {
    function isUsingSoftDelete(string $sourceClass): bool
    {
        return isUsingClass(sourceClass: $sourceClass,targetClass: \Illuminate\Database\Eloquent\SoftDeletes::class);
    }
}
