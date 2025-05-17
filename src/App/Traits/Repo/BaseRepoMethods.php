<?php

namespace Callmeaf\Base\App\Traits\Repo;

use Callmeaf\Base\App\Enums\RandomType;
use Callmeaf\Base\App\Models\Contracts\BaseConfigurable;
use Callmeaf\Base\App\Models\Contracts\HasMedia;
use Callmeaf\Base\App\Traits\Model\HasSearch;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\File\UploadedFile;

trait BaseRepoMethods
{
    public function create(array $data)
    {
        $model = $this->getQuery()->create(attributes: $data);
        $resource = $this->toResource(model: $model);

        $this->eventsCaller($model);

        return $resource;
    }

    public function createQuietly(array $data): int
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

    public function addMedia(mixed $id,UploadedFile $file,?string $collectionName = null,?string $diskName = null,bool $removeOldMedia = true)
    {
        $model = $id instanceof JsonResource ? $id : $this->findById($id);
        if(! ( $model->resource instanceof HasMedia )) {
            throw new \Exception("Model must implements HasMedia.php interface for addMedia");
        }
        /**
         * @var HasMedia $model
         */
        $collectionName ??= $model->mediaCollectionName() ?? 'default';
        $diskName ??= $model->mediaDiskName() ?? '';

        if($removeOldMedia) {
            $model->clearMediaCollection($collectionName);
        }
        $hashedFileName = \Base::random(length: 10) . '.' . $file->getClientOriginalExtension();
        $name = $file->getClientOriginalName();
        return $model->addMedia(file: $file)->usingFileName($hashedFileName)->usingName($name)->toMediaCollection(collectionName: $collectionName,diskName: $diskName);
    }

    public function addMultiMedia(mixed $id,array $files,?string $collectionName = null,?string $diskName = null,bool $removeOldMedia = false)
    {
        $model = $id instanceof JsonResource ? $id : $this->findById($id);
        if(! ($model->resource instanceof HasMedia)) {
            throw new \Exception("Model must implements HasMedia.php interface for addMultiMedia");
        }
        /**
         * @var HasMedia $model
         */
        $collectionName ??= $model->mediaCollectionName() ?? 'default';
        $diskName ??= $model->mediaDiskName() ?? '';

        $media = collect();

        if($removeOldMedia) {
            $model->clearMediaCollection($collectionName);
        }

        foreach ($files as $file) {
            $hashedFileName = \Base::random(length: 10) . '.' . $file->getClientOriginalExtension();
            $name = $file->getClientOriginalName();
            $media->push(
                $model->addMedia($file)->usingFileName($hashedFileName)->usingName($name)->toMediaCollection(collectionName: $collectionName,diskName: $diskName)
            );
        }

        return $media;
    }
}
