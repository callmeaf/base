<?php

namespace Callmeaf\Base\Services\V1;

use Callmeaf\Base\Exceptions\MustInstanceOfException;
use Callmeaf\Base\Services\V1\Contracts\BaseServiceInterface;
use Callmeaf\Media\Enums\MediaCollection;
use Callmeaf\Media\Enums\MediaDisk;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\HasMedia;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class BaseService implements BaseServiceInterface
{
    /**
     * @param Builder|null $query
     * @param Model|null $model
     * @param Collection|null $collection
     * @param string|null $resource instance of JsonResource
     * @param string|null $resourceCollection instance of ResourceCollection
     * @param array $defaultData
     * @param array $searchableColumns
     */
    public function __construct(
        protected ?Builder    $query = null,
        protected ?Model      $model = null,
        protected ?Collection $collection = null,
        protected ?string     $resource = null,
        protected ?string     $resourceCollection = null,
        protected array       $defaultData = [],
        protected array       $searchableColumns = [],
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

    public function freshQuery(): BaseService
    {
        $this->query = $this->getModelFromQuery()::query();
        return $this;
    }

    public function getModel(bool $asResource = false,array $attributes = []): Model|JsonResource|null
    {
        $model = $this->model;
        if(is_null($model)) {
            return $model;
        }
        if ($asResource) {
            $model = new $this->resource($model,$attributes);
        }
        return $model;
    }

    public function getModelFromQuery(): Builder|Model
    {
        return $this->query->getModel();
    }

    public function setModel(Model $model): BaseService
    {
        $this->model = $model;
        return $this;
    }

    public function freshModel(): BaseService
    {
        $this->model = $this->model->fresh();
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

    public function where(callable|string|array $column, array|string|null $valueOrOperation = null, array|string|null $value = null): BaseService
    {
        switch (true) {
            case is_callable($column) || is_array($column): {
                $this->query->where($column);
                break;
            }
            case is_array($valueOrOperation) && is_string($column): {
                $this->query->whereIn($column,$valueOrOperation);
                break;
            }
            default: {
                $this->query->where($column,$valueOrOperation,$value);
            }
        }

        return $this;
    }

    public function exists(): bool
    {
        return $this->query->exists();
    }

    public function first(): BaseService
    {
        $this->model = $this->query->first();
        return $this;
    }

    public function all(array $relations = [], array $columns = ['*'], array $filters = [], ?int $perPage = null, ?int $page = null): BaseService
    {
        // TODO: Implement all() method.
    }

    public function create(array $data): BaseService
    {
        $this->model = $this->query->create(
            $this->mergeData($data)
        );

        return $this;
    }

    public function update(array $data): BaseService
    {
        $this->model->update($data);
        $this->model = $this->model->refresh();
        return $this;
    }

    public function updateOrCreate(array $identifies, array $data): BaseService
   {
       $model = $this->freshQuery()->where($identifies)->first()->getModel();
       if($model) {
           $this->update($data);
       } else {
           $this->create($this->mergeData($data));
       }
        return $this;
   }

   public function delete(): BaseService
   {
       $this->model->delete();
       return $this;
   }

   public function forceDelete(int|string|null $id = null,string $column = 'id'): BaseService
   {
       if(!$this->model) {
           $this->freshQuery()->where($column,$id)->first();
       }
        $this->model->forceDelete();
        return $this;
   }

   public function createMedia(UploadedFile $file, MediaCollection $collection, MediaDisk $disk,bool $removeOlderMedia = true): BaseService
   {
       if(!($this->model instanceof HasMedia)) {
            throw new MustInstanceOfException(__('callmeaf-base::v1.errors.must_instance_if', ['target' => 'Model', 'source' => ' \Spatie\MediaLibrary\HasMedia']));
       }
       if($removeOlderMedia) {
           $this->model->clearMediaCollection(collectionName: $collection->value);
       }

       $this->model->addMedia(file: $file)->toMediaCollection(collectionName: $collection->value,diskName: $disk->value);
        return $this;
   }

    public function mergeData(array $data): array
    {
        return array_merge($this->defaultData,$data);
    }

    protected function search(array $filters = []): BaseService
    {
        $filters = collect($filters);
        $searchableColumns = [
            ...config('callmeaf-base.searchable_columns'),
            ...$this->searchableColumns,
        ];
        foreach ($searchableColumns as $key => $column) {
            $value = $filters->get($key);
            if(is_null($value)) {
                continue;
            }
            if(is_array($column)) {
                [$columnName,$operation] = $column;
                $this->query->where($columnName,$operation,$value);
            } else {
                $this->query->where($column,$value);
            }
        }

        $filters = $filters->filter(fn($item) => strlen(trim($item)))->values(); // remove null values
        $this->query->where(function ($query) use ($searchableColumns,$filters) {
            foreach ($searchableColumns as $key => $column) {
                $value = $filters->get($key);
                $query->orWhere($key,'like',"%$value%");
            }
        });


        return $this;
    }
}
