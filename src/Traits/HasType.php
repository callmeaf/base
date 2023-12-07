<?php

namespace Callmeaf\Base\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Log;

trait HasType
{
    public function scopeOfType(Builder $query,int $type)
    {
        $query->where('type',$type);
    }
    public function typeText(): Attribute
    {
        Log::alert($this->type->value);
        Log::alert($this->type->name);
        Log::alert(json_encode($this::enumsLang()));
        return Attribute::make(
            get: fn() => enumTranslator($this->type,$this::enumsLang()),
        );
    }

    public function isType(int $type): bool
    {
        return $this->type->value === $type;
    }
}
