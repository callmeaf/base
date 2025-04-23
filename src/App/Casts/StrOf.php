<?php

namespace Callmeaf\Base\App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsInboundAttributes;
use Illuminate\Database\Eloquent\Model;

class StrOf implements CastsInboundAttributes
{
    /**
     * Create a new cast class instance.
     */
    public function __construct(
        protected string $methods = '',
    ) {}
    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        $value = str($value);

        $methods = explode(',',$this->methods);
        $methods = array_filter($methods);
        foreach ($methods as $method) {
            $value = $value->$method();
        }
        return $value->toString();
    }
}
