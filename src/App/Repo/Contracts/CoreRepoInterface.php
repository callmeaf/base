<?php

namespace Callmeaf\Base\App\Repo\Contracts;

use Callmeaf\Base\App\Enums\ExportType;
use Callmeaf\Base\App\Enums\ImportType;
use Callmeaf\Base\App\Models\Contracts\BaseConfigurable;
use Callmeaf\Base\App\Services\Importer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\LazyCollection;

/**
 * @template TModel
 * @template TResource of JsonResource<TModel>
 * @template TResourceCollection of ResourceCollection<TResource>
 */
interface CoreRepoInterface
{
    /**
     * @return Builder<TModel>
     */
    public function getQuery(bool $fresh = false): Builder;

    public function freshQuery(): self;

    /**
     * @return TModel
     */
    public function getModel();

    public function getTable(): string;

    public function trashed(bool $only = true): self;
    /**
     * @param string $column
     * @param mixed $value
     * @return TResource
     */
    public function findBy(string $column, mixed $value);

    /**
     * @param mixed $value
     * @return TResource
     */
    public function findById(mixed $value);

    public function enums(): JsonResponse;
    /**
     * @param callable(Builder): void $closure
     * @return self
     */
    public function builder(callable $closure): self;

    public function orderBy(string $column, $direction = 'asc'): self;

    public function latest(string $column = 'created_at'): self;

    public function export(ExportType $type);

    public function import(ImportType $type, $file): Importer;

    /**
     * @param MissingValue|BaseConfigurable $model
     * @return TResource
     */
    public function toResource(MissingValue|BaseConfigurable $model);

    /**
     * @param Collection|LengthAwarePaginator|LazyCollection|MissingValue $collection
     * @return TResourceCollection
     */
    public function toResourceCollection(Collection|LengthAwarePaginator|LazyCollection|MissingValue $collection);
}
