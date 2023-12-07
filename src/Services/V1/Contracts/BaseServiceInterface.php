<?php

namespace Callmeaf\Base\Services\V1\Contracts;

use Callmeaf\Base\Services\V1\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

interface BaseServiceInterface
{
    public function getQuery(): Builder;
    public function setQuery(Builder $query): BaseService;

    public function getModel(bool $asResource = false): Model|JsonResource;
    public function setModel(Model $model): BaseService;

    public function getCollection(bool $asResourceCollection = false): Collection|LengthAwarePaginator|ResourceCollection;
    public function setCollection(Collection $collection): BaseService;
    public function create(array $data): BaseService;
    public function mergeData(array $data): array;
}
