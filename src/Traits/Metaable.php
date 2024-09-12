<?php

namespace Callmeaf\Base\Traits;

use Callmeaf\Base\Contracts\HasMeta;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait Metaable
{
    protected static function bootHasMeta(): void
    {
        static::created(function(HasMeta $model) {
            $model->meta()->create([
                'data' => $model->metaData(),
            ]);
        });
    }

    public function meta(): MorphOne
    {
        return $this->morphOne(config('callmeaf-meta.model'),'metaable');
    }
}
