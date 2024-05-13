<?php

namespace Callmeaf\Base\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasParent
{
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class,'parent_id','id');
    }
}
