<?php

namespace Callmeaf\Base\Traits;

use Callmeaf\Media\Enums\MediaCollection;
use Callmeaf\Media\Models\Media;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Collection;

trait HasMediaMethod
{
    public function file(MediaCollection $mediaCollection): ?Media
    {
        if($this->relationLoaded('media')) {
            return $this->media->firstWhere('collection_name',$mediaCollection->value);
        }
        return $this->media()->firstWhere('collection_name',$mediaCollection->value);
    }

    public function files(MediaCollection $mediaCollection): Collection|\Illuminate\Database\Eloquent\Collection
    {
        if($this->relationLoaded('media')) {
            return $this->media->where('collection_name',$mediaCollection->value)->values();
        }
        return $this->media()->where('collection_name',$mediaCollection->value)->get();
    }
    public function image(): Attribute
    {
        return Attribute::get(
            fn() => $this->file(MediaCollection::IMAGE),
        );
    }

    public function documents(): Attribute
    {
        return Attribute::get(
            fn() => $this->files(MediaCollection::DOCUMENTS),
        );
    }

}
