<?php

namespace Callmeaf\Base\App\Traits\Model;

trait BasePivotModelMethods
{
    abstract static public function configKey(): string;

    public static function config(): array
    {
        return config(static::configKey() . '-' . requestVersion());
    }
}
