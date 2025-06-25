<?php

namespace Callmeaf\Base\App\Traits\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasParent
{
    public function scopeParent(Builder $query): void
    {
        $query->whereNull($this->parentColumnName());
    }

    public function scopeChildren(Builder $query): void
    {
        $query->whereNotNull($this->parentColumnName());
    }

    public function scopeChildrenOf(Builder $query,string|int $parentId): void
    {
        $query->where($this->parentColumnName(),$parentId);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class,$this->parentColumnName(),$this->getRouteKeyName());
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class,$this->parentColumnName(),$this->getRouteKeyName());
    }

    public function isParent(): bool
    {
        return empty($this->{$this->parentColumnName()});
    }

    public function isChildren(): bool
    {
        return ! $this->isParent();
    }

    public function parentColumnName(): string
    {
        return 'parent_id';
    }
}
