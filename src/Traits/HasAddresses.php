<?php

namespace Callmeaf\Base\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasAddresses
{
    public function addresses(): MorphMany
    {
        return $this->morphMany(config('callmeaf-address.model'),'addressable');
    }

    public function defaultAddress(): MorphOne
    {
        return $this->addresses()->where(column: 'is_default',operator: true)->one();
    }
}
