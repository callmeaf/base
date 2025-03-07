<?php

namespace Callmeaf\Base\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

trait Localeable
{
    protected static function bootLocaleable(): void
    {
        static::creating(function(Model $model) {
            $model->forceFill([
                'locale' => $model->locale ?? App::currentLocale(),
            ]);
        });

        static::addGlobalScope(localScope());
    }
}
