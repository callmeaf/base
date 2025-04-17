<?php

namespace Callmeaf\Base\App\Traits\Model;

use Callmeaf\Base\App\Models\Contracts\BaseConfigurable;

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
}
