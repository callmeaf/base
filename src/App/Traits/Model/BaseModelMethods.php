<?php

namespace Callmeaf\Base\App\Traits\Model;

use Callmeaf\Base\App\Models\Contracts\BaseConfigurable;
use Callmeaf\Base\Contracts\Model\HasSearch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait BaseModelMethods
{
    abstract static public function configKey(): string;

    public static function config(): array
    {
        return config(static::configKey() . '-' . requestVersion());
    }

    public function getRouteKeyName(): string
    {
        $primaryKey = $this->primaryKey;
        if ($this instanceof BaseConfigurable) {
            return self::config()['route_key_name'] ?? $primaryKey;
        }

        return $primaryKey;
    }

    public function handleSearch(Builder $query): void
    {
        if (! ($this instanceof HasSearch)) {
            return;
        }

        [, $params] = $this->searchParams();
        $request = request();

        $query->where(function (Builder $builder) use ($params, $request) {
            foreach ($params as $param => $column) {
                $value = trim($request->query($param));
                if (! $value) {
                    continue;
                }

                match (true) {
                    str($param)->contains('_from') => $builder->whereDate($column, ">=", $value),
                    str($param)->contains('_to') => $builder->whereDate($column, '<=', $value),
                    default => $builder->where($column, $value)
                };
            }
        });
    }
}
