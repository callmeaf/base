<?php

namespace Callmeaf\Base\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasDate
{
    public function createdAtText(): Attribute
    {
        return Attribute::make(
            get: fn() => verta($this->created_at)->format('Y-m-d H:i:s'),
        );
    }

    public function updatedAtText(): Attribute
    {
        return Attribute::make(
            get: fn() => verta($this->updated_at)->format('Y-m-d H:i:s'),
        );
    }
}
