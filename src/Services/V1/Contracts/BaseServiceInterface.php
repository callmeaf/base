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
    public function freshQuery(): BaseService;
    public function getModel(bool $asResource = false,array $attributes = []): Model|JsonResource|null;
    public function getModelFromQuery(): Builder|Model;
    public function setModel(Model $model): BaseService;
    public function getCollection(bool $asResourceCollection = false): Collection|LengthAwarePaginator|ResourceCollection;
    public function setCollection(Collection $collection): BaseService;
    public function where(string|callable|array $column,string|array|null $valueOrOperation = null,null|string|array $value = null): BaseService;
    public function exists(): bool;
    public function first(): BaseService;
    public function create(array $data): BaseService;
    public function update(array $data): BaseService;
    public function updateOrCreate(array $identifies,array $data): BaseService;
    public function delete(): BaseService;
    public function forceDelete(int|string|null $id = null,string $column = 'id'): BaseService;
    public function mergeData(array $data): array;
}
