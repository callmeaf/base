<?php

namespace Callmeaf\Base\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasAuthor
{
    protected static function bootHasAuthor(): void
    {
        static::creating(function(Model $model) {
            $model->forceFill([
                'author_id' => request()->get('author_id') ?? authId(),
            ]);
        });
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'),'author_id','id');
    }
}
