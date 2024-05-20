<?php

namespace Callmeaf\Base\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasType
{
    public function scopeOfType(Builder $query,string|int $type)
    {
        $query->where('type',$type);
    }
    public function typeText(): Attribute
    {
        return Attribute::make(
            get: fn() => enumTranslator($this->type,$this::enumsLang()),
        );
    }

    public function isType(string|int $type): bool
    {
        return $this->type->value === $type;
    }
}
