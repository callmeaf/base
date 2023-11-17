<?php

namespace Callmeaf\Base\Services\V1;

use Callmeaf\Base\Services\V1\Contracts\BaseServiceInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class BaseService implements BaseServiceInterface
{
    public function __construct(protected ?Builder $query = null,protected ?Model $model = null,protected ?Collection $collection = null,protected array $defaultData = [])
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

    public function getModel(): Model
    {
        return $this->model;
    }

    public function setModel(Model $model): BaseService
    {
        $this->model = $model;
        return $this;
    }

    public function getCollection(): Collection
    {
        return $this->collection;
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
