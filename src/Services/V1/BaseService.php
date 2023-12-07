<?php

namespace Callmeaf\Base\Services\V1;

use Callmeaf\Base\Services\V1\Contracts\BaseServiceInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class BaseService implements BaseServiceInterface
{
    /**
     * @param Builder|null $query
     * @param Model|null $model
     * @param Collection|null $collection
     * @param string|null $resource instance of JsonResource
     * @param string|null $resourceCollection instance of ResourceCollection
     * @param array $defaultData
     */
    public function __construct(
        protected ?Builder    $query = null,
        protected ?Model      $model = null,
        protected ?Collection $collection = null,
        protected ?string     $resource = null,
        protected ?string     $resourceCollection = null,
        protected array       $defaultData = [],
    )
    {

    }

    public function getQuery(): Builder
    {
        return $this->query;
    }

    public function setQuery(Builder $query): BaseService
    {
        $this->query = $query;
        return $this;
    }

    public function getModel(bool $asResource = false): Model|JsonResource
    {
        $model = $this->model;
        if ($asResource) {
            $model = new $this->resource($model);
        }
        return $model;
    }

    public function setModel(Model $model): BaseService
    {
        $this->model = $model;
        return $this;
    }

    public function getCollection(bool $asResourceCollection = false): Collection|LengthAwarePaginator|ResourceCollection
    {
        $collection = $this->collection;
        if ($asResourceCollection) {
            $collection = new $this->resourceCollection($collection);
        }
        return $collection;
    }

    public function setCollection(Collection $collection): BaseService
    {
        $this->collection = $collection;
        return $this;
    }

    public function create(array $data): BaseService
    {
        $this->model = $this->query->create(
            $this->mergeData($data)
        );

        return $this;
    }

    public function mergeData(array $data): array
    {
        return array_merge($this->defaultData,$data);
    }
}
