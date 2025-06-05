<?php

namespace Callmeaf\Base\App\Traits\Model;

use Callmeaf\Base\App\Enums\DateTimeFormat;
use Illuminate\Database\Eloquent\Builder;

trait Publishable
{
    public function scopePublished(Builder $query): void
    {
        $query->where(function(Builder $builder) {
            $builder->whereNull('published_at')->orWhereNowOrPast('published_at');
        });
    }

    public function scopeScheduled(Builder $query): void
    {
        $query->whereFuture('published_at');
    }

    public function isPublished(): bool
    {
        return is_null($this->published_at) || now()->greaterThanOrEqualTo($this->published_at);
    }

    public function isScheduled(): bool
    {
        return ! $this->isPublished();
    }

    public function publishedAtText(DateTimeFormat $format = DateTimeFormat::DATE_TIME)
    {
        return match (app()->currentLocale()) {
            'fa' => $this->published_at ? verta($this->published_at)->format($format->value) : null,
            default =>  $this->published_at ? $this->published_at->format($format->value) : null,
        };
    }

}
