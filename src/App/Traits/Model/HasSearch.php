<?php

namespace Callmeaf\Base\App\Traits\Model;

use Illuminate\Database\Eloquent\Builder;

trait HasSearch
{
    public function scopeSearch(Builder $query): void
    {
        [$likeParams,$exactParams] = $this->searchParams();

        $this->handleSearchLikeParams(query: $query,params: $likeParams);
        $this->handleSearchExactParams(query: $query,params: $exactParams);
    }

    public function handleSearchLikeParams(Builder $query,array $params): void
    {
        $request = request();
        $query->where(function (Builder $builder) use ($params, $request) {
            foreach ($params as $param => $column) {
                $value = trim($request->query($param));
                if (! $value) {
                    continue;
                }
                $value = \Base::searchValue($value);

                if ($param === array_key_first($params)) {
                    $builder->whereLike($column, $value);
                } else {
                    $builder->orWhereLike($column, $value);
                }
            }
        });
    }

    public function handleSearchExactParams(Builder $query,array $params): void
    {
        $request = request();
        $query->where(function (Builder $builder) use ($params, $request) {
            foreach ($params as $param => $column) {
                $value = trim($request->query($param));
                if (! $value) {
                    continue;
                }

                match (true) {
                    str($param)->contains('_from') => $builder->whereDate($column, ">=", $value),
                    str($param)->contains('_to') => $builder->whereDate($column, '<=', $value),
                    default => $builder->where($column, $value)
                };
            }
        });
    }

    abstract public function searchParams(): array;
}
