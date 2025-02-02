<?php

namespace Callmeaf\Base\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasParent
{
    abstract public function parentModel(): string;
    public function parent(): BelongsTo
    {
        return $this->belongsTo($this->parentModel(),'parent_id',$this->getKey());
    }

    public function hasParent(): bool
    {
        return !is_null($this->parent_id);
    }
}
