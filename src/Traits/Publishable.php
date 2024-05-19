<?php

namespace Callmeaf\Base\Traits;


use Illuminate\Database\Eloquent\Casts\Attribute;

trait Publishable
{
    public function publishedAtText(): Attribute
    {
        return Attribute::make(
            get: fn() => verta($this->published_at)->format('Y-m-d H:i:s'),
        );
    }

    public function expiredAtText(): Attribute
    {
        return Attribute::make(
            get: fn() => verta($this->expired_at)->format('Y-m-d H:i:s'),
        );
    }
}
