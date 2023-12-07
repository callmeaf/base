<?php

namespace Callmeaf\Base\Traits;

use Illuminate\Database\Eloquent\Builder;
use \Illuminate\Database\Eloquent\Casts\Attribute;

trait HasStatus
{
    public function scopeActive(Builder $query,int $active = 1)
    {
        $query->where('status',$active);
    }

    public function scopeInActive(Builder $query,int $inactive = 2)
    {
        $query->where('status',$inactive);
    }

    public function statusText(): Attribute
    {
        return Attribute::make(
            get: fn() => enumTranslator($this->status,$this::enumsLang()),
        );
    }

    public function isActive(int|string $active = 1): bool
    {
        return $this->status->value === $active;
    }

    public function isInActive(int|string $inactive = 2): bool
    {
        return $this->status->value === $inactive;
    }
}
