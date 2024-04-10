<?php

namespace Callmeaf\Base\Utilities\V1\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface SearcherInterface
{
    public function apply(Builder $query,array $filters = []): void;
}
