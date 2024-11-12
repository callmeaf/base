<?php

namespace Callmeaf\Base\Traits;

use Callmeaf\Slug\Models\Slug;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait Sluggable
{
    public function slug(): MorphOne
    {
        return $this->morphOne(Slug::class,'sluggable');
    }
}
