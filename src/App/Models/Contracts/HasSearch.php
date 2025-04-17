<?php

namespace Callmeaf\Base\App\Models\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface HasSearch
{
    public function scopeSearch(Builder $query): void;

    public function searchParams(): array;
}
