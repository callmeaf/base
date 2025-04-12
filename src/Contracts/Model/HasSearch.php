<?php

namespace Callmeaf\Base\Contracts\Model;

use Illuminate\Database\Eloquent\Builder;

interface HasSearch
{
    public function scopeSearch(Builder $query): void;

    public function searchParams(): array;
}
