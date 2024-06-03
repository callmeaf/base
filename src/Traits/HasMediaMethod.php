<?php

namespace Callmeaf\Base\Traits;

use Callmeaf\Media\Enums\MediaCollection;
use Callmeaf\Media\Models\Media;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasMediaMethod
{
    public function file(MediaCollection $mediaCollection): ?Media
    {
        if($this->relationLoaded('media')) {
            return $this->media->firstWhere('collection_name',$mediaCollection->value);
        }
        return $this->media()->firstWhere('collection_name',$mediaCollection->value);
    }
    public function image(): Attribute
    {
        return Attribute::get(
            fn() => $this->file(MediaCollection::IMAGE),
        );
    }

}
