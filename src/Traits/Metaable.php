<?php

namespace Callmeaf\Base\Traits;

use Callmeaf\Base\Services\V1\BaseService;
use Callmeaf\Meta\Events\MetaCreated;
use Callmeaf\Meta\Events\MetaDeleted;
use Callmeaf\Meta\Events\MetaUpdated;
use Callmeaf\Meta\Services\V1\MetaService;
use Callmeaf\Meta\Utilities\V1\Api\Meta\MetaData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait Metaable
{
    protected static function bootMetaable(): void
    {
        $isUsingSoftDelete = isUsingSoftDelete(sourceClass: self::class);
        /**
         * @var BaseService $baseService
         */
        $baseService = app(BaseService::class);
        /**
         * @var MetaService $metaService
         */
        $metaService = app(config('callmeaf-meta.service'));

        static::created(function(Model $model) use ($baseService,$metaService) {
            $metaData = @$model->metaData()['created'];
            if(! is_null($metaData)) {
                $baseService->setModel(model: $model)->updateMeta(data: $metaData);
                $model = $model->load([
                    'meta'
                ]);
                $metaService->setModel(model: $model->meta)->getModel(events: [
                    MetaCreated::class,
                ]);
            }
        });

        static::updated(function(Model $model) use ($baseService,$metaService) {
            if(! $model->wasChanged(['deleted_at'])) {
                $metaData = @$model->metaData()['updated'];
                if(! is_null($metaData)) {
                    $baseService->setModel(model: $model)->updateMeta(data: $metaData);
                    $metaService->setModel(model: $model->meta)->getModel(events: [
                        MetaUpdated::class
                    ]);
                }
            }
        });

        static::deleted(function(Model $model) use ($isUsingSoftDelete,$baseService,$metaService) {
            $metaData = @$model->metaData()['deleted'];
            if($isUsingSoftDelete) {
                if(! is_null($metaData)) {
                    $baseService->setModel(model: $model)->updateMeta(data: $metaData);
                    $metaService->setModel(model: $model->meta)->getModel(events: [
                        MetaUpdated::class,
                    ]);
                }
            } else {
                if($model->meta) {
                    $metaService->setModel(model: $model->meta)->delete(events: [
                        MetaDeleted::class
                    ]);
                }
            }

        });

        if($isUsingSoftDelete) {
            static::restored(function(Model $model) use ($baseService,$metaService) {
                $metaData = @$model->metaData()['restored'];
                if(! is_null($metaData)) {
                    $baseService->setModel(model: $model)->updateMeta(data: $metaData);
                    $metaService->setModel(model: $model->meta)->getModel(events: [
                        MetaUpdated::class
                    ]);
                }
            });

            static::forceDeleted(function(Model $model) use ($metaService) {
                if($model->meta) {
                    $metaService->setModel(model: $model->meta)->delete(events: [
                        MetaDeleted::class
                    ]);
                }
            });
        }
    }

    public function meta(): MorphOne
    {
        return $this->morphOne(config('callmeaf-meta.model'),'metaable');
    }

    /**
     * Each key must contain lifecycle of eloquent (created,updated,deleted)
     * if each key return null -> lifecycle never fired
     * if each key return [] -> meta data empty after fired lifecycle
     * if each key return ['key' => 'value'] -> old meta data merge with new meta data
     * @return array|null
     */
    public function metaData(): ?array
    {
        return MetaData::from($this)->make();
    }
}
