<?php

namespace Callmeaf\Base\App\Traits\Repo;

use Callmeaf\Base\App\Traits\Model\HasSearch;
use Illuminate\Database\Eloquent\Model;

trait BaseRepoMethods
{
    use MediaMethods;
    public function create(array $data)
    {
        $model = $this->getQuery()->create(attributes: $data);
        $resource = $this->toResource(model: $model);

        $this->eventsCaller($model);

        return $resource;
    }

    public function createQuietly(array $data)
    {
        return $this->getQuery()->create(attributes: $data);
    }

    public function all()
    {
        $collection = $this->getQuery()->get();

        $this->eventsCaller($collection);

        return $this->toResourceCollection(collection: $collection);
    }

    public function paginate(?int $perPage = null, ?int $page = null)
    {
        $request = request();

        $perPage ??= $request->query(\Base::config(key: 'per_page_key'));
        $maxPerPage = \Base::config(key: 'max_per_page');
        if(intval($perPage) > $maxPerPage) {
            $perPage = $maxPerPage;
        }

        $page ??= $request->query(\Base::config(key: 'page_key'));

        $collection = $this->getQuery()->paginate(perPage: $perPage, page: $page);

        $this->eventsCaller($collection);

        return $this->toResourceCollection(collection: $collection);
    }

    public function lazy()
    {
        $collection = $this->getQuery()->lazyById(chunkSize: 1000, column: $this->modelKeyName());

        return $this->toResourceCollection(collection: $collection);
    }

    public function search(?callable $builder = null): self
    {
        if ($builder) {
            $this->builder($builder);
        } else if (\Base::classUse($this->getModel()::class,HasSearch::class)) {
            $this->getQuery()->search();
        }

        return $this;
    }

    public function update(mixed $id, array $data)
    {
        if(count($data) === 1 && array_keys($data) === ['status']) {
            return $this->statusUpdate(id: $id,value: $data['status']);
        }

        if(count($data) === 1 && array_keys($data) === ['type']) {
            return $this->typeUpdate(id: $id,value: $data['type']);
        }

        $model = $this->findById(value: $id);
        $model = tap($model->resource)->update($data);

        $this->eventsCaller($model);

        return $this->toResource(model: $model);
    }

    public function updateQuietly(mixed $id, array $data): int
    {
        return $this->getQuery()->where(column: $this->modelKeyName(), operator: $id)->update(values: $data);
    }

    public function statusUpdate(mixed $id,mixed $value)
    {
        $model = $this->findById(value: $id);
        $model = tap($model->resource)->update([
            'status' => $value,
        ]);

        $this->eventsCaller($model);

        return $this->toResource(model: $model);
    }

    public function typeUpdate(mixed $id,mixed $value)
    {
        $model = $this->findById(value: $id);
        $model = tap($model->resource)->update([
            'type' => $value
        ]);

        $this->eventsCaller($model);

        return $this->toResource(model: $model);
    }

    public function delete(mixed $id)
    {
        $model = $this->findById(value: $id);
        $model = tap($model->resource)->delete();

        $this->eventsCaller($model);

        return $this->toResource(model: $model);
    }

    public function deleteQuietly(mixed $id): int
    {
        return $this->getQuery()->where(column: $this->modelKeyName(), operator: $id)->delete();
    }

    public function restore(mixed $id)
    {
        $this->trashed();
        $model = $this->findById(value: $id);
        $model = tap($model->resource)->restore();

        $this->eventsCaller($model);

        return $this->toResource(model: $model);
    }

    public function restoreQuietly(mixed $id): int
    {
        return $this->trashed()->getQuery()->where(column: $this->modelKeyName(), operator: $id)->restore();
    }

    public function forceDelete(mixed $id)
    {
        $this->trashed();
        $model = $this->findById(value: $id);
        $model = tap($model->resource)->forceDelete();

        $this->eventsCaller($model);

        return $this->toResource(model: $model);
    }

    public function forceDeleteQuietly(mixed $id): int
    {
        return $this->trashed()->getQuery()->where(column: $this->modelKeyName(), operator: $id)->forceDelete();
    }
}
