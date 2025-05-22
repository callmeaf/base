<?php

namespace Callmeaf\Base\App\Traits\Repo;

use Callmeaf\Base\App\Models\Contracts\HasMedia;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\File\UploadedFile;

trait MediaMethods
{
    public function addMedia(JsonResource|string|int $id,UploadedFile $file,?string $collectionName = null,?string $diskName = null,bool $removeOldMedia = true)
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

    public function addMultiMedia(JsonResource|string|int $id,array $files,?string $collectionName = null,?string $diskName = null,bool $removeOldMedia = false)
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
