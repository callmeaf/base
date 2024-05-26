<?php

namespace Callmeaf\Base\Services\V1\Contracts;

use Callmeaf\Base\Services\V1\BaseService;
use Callmeaf\Media\Enums\MediaCollection;
use Callmeaf\Media\Enums\MediaDisk;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface BaseServiceInterface
{
    public function getQuery(): Builder;
    public function setQuery(Builder $query): BaseService;
    public function freshQuery(): BaseService;
    public function getModel(bool $asResource = false,array $attributes = [],array $relations = [],?array $events = []): Model|JsonResource|null;
    public function getModelFromQuery(): Builder|Model;
    public function setModel(Model $model): BaseService;
    public function freshModel(): BaseService;
    public function getCollection(bool $asResourceCollection = false,bool $asResponseData = false,array $attributes = []): Collection|LengthAwarePaginator|ResourceCollection|array|null;
    public function setCollection(Collection|LengthAwarePaginator|ResourceCollection $collection): BaseService;
    public function where(string|callable|array $column,string|array|null $valueOrOperation = null,null|string|array $value = null): BaseService;
    public function select(array $columns = ['*']): BaseService;
    public function onlyTrashed(): BaseService;
    public function exists(): bool;
    public function first(array $columns = ['*'],bool $failed = true): BaseService;
    public function all(array $relations = [],array $columns = ['*'],array $filters = [],?int $perPage = null,?int $page = null,?array $events = []): BaseService;
    public function create(array $data,?array $events = []): BaseService;
    public function update(array $data,?array $events = []): BaseService;
    public function updateOrCreate(array $identifies,array $data): BaseService;
    public function delete(?array $events = []): BaseService;
    public function restore(string|int $id,string $idColumn = 'id',array $columns = ['*'],?array $events = []): BaseService;
    public function forceDelete(string|int|null $id = null,string $idColumn = 'id',array $columns = ['*'],?array $events = []): BaseService;
    public function createMedia(?UploadedFile $file,MediaCollection $collection,MediaDisk $disk,bool $removeOlderMedia = true,?array $events = []): BaseService;
    public function mergeData(array $data): array;
}
