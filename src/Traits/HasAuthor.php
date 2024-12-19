<?php

namespace Callmeaf\Base\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasAuthor
{
    protected static function bootHasAuthor(): void
    {
        static::creating(function(Model $model) {
            $authUser = authUser();
            $author_id = null;
            // Only super admin or admin can change author id in model
            if($authUser && $authUser?->isSuperAdminOrAdmin()) {
                $author_id = request()->get('author_id');
            }

            $author_id = $author_id ?? $authUser?->id;
            $model->forceFill([
                'author_id' => $author_id,
            ]);
        });
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'),'author_id','id');
    }
}
