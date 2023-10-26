<?php

namespace Callmeaf\Base\Services\V1\Contracts;

use Callmeaf\Base\Services\V1\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface BaseServiceInterface
{
    public function getQuery(): Builder;
    public function setQuery(Builder $query): BaseService;
    public function getModel(): Model;
    public function setModel(Model $model): BaseService;
    public function getCollection(): Collection;
    public function setCollection(Collection $collection): BaseService;
    public function create(array $data): BaseService;
    public function mergeData(array $data): array;
}
