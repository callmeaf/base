<?php

namespace Callmeaf\Base\App\Traits\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasParent
{
    public function scopeParent(Builder $query): void
    {
        $query->whereNull('parent_id');
    }

    public function scopeChildren(Builder $query): void
    {
        $query->whereNotNull('parent_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class,'parent_id',$this->getRouteKeyName());
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class,'parent_id',$this->getRouteKeyName());
    }
}
