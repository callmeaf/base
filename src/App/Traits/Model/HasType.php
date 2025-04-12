<?php

namespace Callmeaf\Base\App\Traits\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasType
{
    public function scopeOfType(Builder $query, mixed $value): void
    {
        $query->where('type', $value);
    }

    public function isType(mixed $value): bool
    {
        return $this->type == $value;
    }

    public function typeText(): Attribute
    {
        return Attribute::get(fn() => \Base::enumCaseTranslator(
            \Base::getPackageNameFromModel(model: self::class),
            $this->type,
        ));
    }
}
