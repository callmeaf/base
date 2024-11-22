<?php

namespace Callmeaf\Base\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasChildren
{
    abstract public function childrenModel(): string;
    public function children(): HasMany
    {
        return $this->hasMany($this->childrenModel(),'parent_id',$this->getKey());
    }
}
