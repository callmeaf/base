<?php

namespace Callmeaf\Base\Services\V1;

use Callmeaf\Base\Exceptions\MustInstanceOfException;
use Callmeaf\Base\Services\V1\Contracts\BaseServiceInterface;
use Callmeaf\Base\Utilities\V1\Contracts\SearcherInterface;
use Callmeaf\Media\Enums\MediaCollection;
use Callmeaf\Media\Enums\MediaDisk;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\LazyCollection;
use Spatie\MediaLibrary\HasMedia;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class BaseService implements BaseServiceInterface
{
    /**
     * @param Builder|null $query
     * @param Model|null $model
     * @param Collection|LengthAwarePaginator|ResourceCollection|LazyCollection|array|null $collection
     * @param string|null $resource instance of JsonResource
     * @param string|null $resourceCollection instance of ResourceCollection
     * @param array $defaultData
     * @param string|null $searcher instance of Callmeaf\Base\Utilities\V1\Contracts\SearcherInterface
     */
    public function __construct(
        protected ?Builder    $query = null,
        protected ?Model      $model = null,
        protected Collection|LengthAwarePaginator|ResourceCollection|LazyCollection|array|null $collection = null,
        protected ?string     $resource = null,
        protected ?string     $resourceCollection = null,
        protected array       $defaultData = [],
        protected ?string  $searcher = null,
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

    public function getModel(bool $asResource = false,array $attributes = [],array $relations = [],?array $events = []): Model|JsonResource|null
    {
        $model = $this->model;
        if(is_null($model)) {
            return $model;
        }
        if(!empty($relations)) {
            $model = $model->loadMissing($relations);
        }
        if ($asResource) {
            $model = new $this->resource($model,$attributes);
        }
        $this->eventsCaller($events);
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

    public function getCollection(bool $asResourceCollection = false,bool $asResponseData = false,array $attributes = [],?array $events = []): Collection|LengthAwarePaginator|ResourceCollection|LazyCollection|array|null
    {
        $collection = $this->collection;
        if ($asResourceCollection) {
            /**
             * @var ResourceCollection $collection
             */
            $collection = new $this->resourceCollection($collection,$attributes);
            if($asResponseData) {
                $collection = $collection->response()->getData(assoc: true);
            }
        }
        $this->eventsCaller($events);
        return $collection;
    }

    public function setCollection(Collection|LengthAwarePaginator|ResourceCollection|LazyCollection $collection): BaseService
    {
        $this->collection = $collection;
        return $this;
    }

    public function where(callable|string|array $column, array|string|null $valueOrOperation = null, array|string|null $value = null,bool $not = false): BaseService
    {
        switch (true) {
            case is_callable($column) || is_array($column): {
                if($not) {
                    $this->query->whereNot($column);
                } else {
                    $this->query->where($column);
                }
                break;
            }
            case is_array($valueOrOperation) && is_string($column): {
                if($not) {
                    $this->query->whereNotIn($column,$valueOrOperation);
                } else {
                    $this->query->whereIn($column,$valueOrOperation);
                }
                break;
            }
            default: {
                if($not) {
                    $this->query->whereNot($column,$valueOrOperation,$value);
                } else {
                    $this->query->where($column,$valueOrOperation,$value);
                }

            }
        }

        return $this;
    }

    public function whereNot(callable|array|string $column, array|string|null $valueOrOperation = null, array|string|null $value = null): BaseService
    {
        return $this->where(column: $column,valueOrOperation: $valueOrOperation,value: $value,not: true);
    }

    public function select(array $columns = ['*']): BaseService
    {
        $this->query->select(columns: $columns);
        return $this;
    }

    public function onlyTrashed(): BaseService
    {
        $this->query->onlyTrashed();
        return $this;
    }

    public function exists(): bool
    {
        return $this->query->exists();
    }

    public function first(array $columns = ['*'],bool $failed = true): BaseService
    {
        if($failed) {
            $this->model = $this->query->firstOrFail($columns);
        } else {
            $this->model = $this->query->first($columns);
        }
        return $this;
    }

    public function orderBy(string $column = 'created_at', string $direction = 'desc'): BaseService
    {
        $this->query->orderBy(column: $column,direction: $direction);
        return $this;
    }

    public function all(array $relations = [], array $columns = ['*'], array $filters = [], ?int $perPage = null, ?int $page = null,?array $events = [],array $orderBy = ['created_at','desc']): BaseService
    {
        $this->orderBy(column: $orderBy[0],direction: $orderBy[1]);
        $this->query->select(columns: $columns)->with(relations: $relations);

        $this->search(filters: $filters);
        $request = request();
        if(config('callmeaf-base.always_paginated')) {
            $page = $page ?? $request->query(config('callmeaf-base.page_key')) ?? config('callmeaf-base.default_page');
            $perPage = $perPage ?? $request->query(config('callmeaf-base.per_page_key')) ?? config('callmeaf-base.default_per_page');
        }

        if($perPage === config('callmeaf-base.all_page_lazy_key') && $request->routeIs(config('callmeaf-base.lazy_routes'))) {
            $this->setCollection($this->query->lazy(chunkSize: config('callmeaf-base.all_page_lazy_chunk_size')));
        } else if ($page && $perPage) {
            if($perPage === config('callmeaf-base.all_page_lazy_key')) {
                $perPage = config('callmeaf-base.default_per_page');
                $page = 1;
            }
            $this->setCollection($this->query->paginate(perPage: $perPage,page: $page));
        } else {
            $this->setCollection($this->query->get());
        }

        $this->eventsCaller($events);
        return $this;
    }

    public function create(array $data,?array $events = []): BaseService
    {
        $this->model = $this->query->create(
            $this->mergeData($data)
        );

        $this->eventsCaller($events);
        return $this;
    }

    public function update(array $data,?array $events = []): BaseService
    {
        $this->model->update($data);
        $this->model = $this->model->refresh();
        $this->eventsCaller($events);
        return $this;
    }

    public function updateOrCreate(array $identifies, array $data): BaseService
   {
       $model = $this->freshQuery()->where($identifies)->first(failed: false)->getModel();
       if($model) {
           $this->update($data);
       } else {
           $this->create($this->mergeData($data));
       }
        return $this;
   }

   public function updateMeta(array $data, ?bool $mergeData = null,bool $createMetaIfNotExists = true,?array $events = []): BaseService
   {
       $meta = $this->model->meta;
       if(is_null($meta)) {
           $meta = $this->model->meta()->create();
       }

       if(is_null($mergeData)) {
           $mergeData = config('callmeaf-meta.merge_old_data_with_new_data');
       }
        if($mergeData) {
            $data = $meta->data->merge($data);
        }

       $meta->update([
            'data' => $data,
        ]);

        $this->eventsCaller($events);

        return $this;
   }

    public function delete(?array $events = []): BaseService
   {
       $this->model->delete();
       $this->eventsCaller($events);
       return $this;
   }

   public function restore(string|int $id,string $idColumn = 'id',array $columns = ['*'],?array $events = []): BaseService
   {
       $this->freshQuery()->onlyTrashed()->where(column: $idColumn,valueOrOperation: $id)->first(columns: $columns);
       $this->model->restore();
       $this->eventsCaller($events);
       return $this;
   }

   public function forceDelete(int|string|null $id = null, string $idColumn = 'id', array $columns = ['*'], ?array $events = []): BaseService
   {
       if(!is_null($id)) {
           $this->freshQuery()->onlyTrashed()->where(column: $idColumn,valueOrOperation: $id)->first(columns: $columns);
       }
       $this->model->forceDelete();
       $this->eventsCaller($events);
       return $this;
   }

    public function createMedia(?UploadedFile $file, MediaCollection $collection, MediaDisk $disk,bool $removeOlderMedia = true,?array $events = []): BaseService
   {
       if(!($file instanceof UploadedFile)) {
           throw new FileNotFoundException('');
       }
       if(!($this->model instanceof HasMedia)) {
            throw new MustInstanceOfException(__('callmeaf-base::v1.errors.must_instance_if', ['target' => 'Model', 'source' => ' \Spatie\MediaLibrary\HasMedia']));
       }
       if($removeOlderMedia) {
           $this->model->clearMediaCollection(collectionName: $collection->value);
       }

       $this->model->addMedia(file: $file)->toMediaCollection(collectionName: $collection->value,diskName: $disk->value);
       $this->eventsCaller($events);
        return $this;
   }

   public function createMultiMedia(?array $files, MediaCollection $collection, MediaDisk $disk, bool $removeOlderMedia = true, ?array $events = []): BaseService
   {
       $files = $files ?? [];
       foreach ($files as $file) {
           $this->createMedia(file: $file,collection: $collection,disk: $disk,removeOlderMedia: $removeOlderMedia,events: $events);
       }
       return $this;
   }

    public function mergeData(array $data): array
    {
        return array_merge($this->defaultData,$data);
    }

    protected function search(array $filters = []): BaseService
    {
        if(isOnlyTrashedQueryParam(params: $filters)) {
            $this->onlyTrashed();
        }
        $this->query->where(function(Builder $query) use ($filters) {
            $baseSearcher = config('callmeaf-base.searcher');
            if($baseSearcher) {
                /**
                 * @var SearcherInterface|null $baseSearcher
                 */
                $baseSearcher = app($baseSearcher);
                $baseSearcher->apply(query: $query,filters: $filters);
            }
        });
        $this->query->where(function (Builder $query) use ($filters) {
            $searcher = $this->searcher;
            if($searcher) {
                /**
                 * @var SearcherInterface|null $searcher
                 */
                $searcher = app($searcher);
                $searcher->apply(query: $query,filters: $filters);
            }
        });

        return $this;
    }

    protected function searchSymbol(string $value,?string $symbol = null): string
    {
        return match ($symbol) {
            '%' => "%$value",
            "%%" => "%$value%",
            default => $value,
        };
    }

    protected function eventsCaller(?array $events = []): void
    {
        foreach ($events as $event) {
            $event::dispatch($this->model ?? $this->collection);
        }
    }
}
