<?php

namespace Callmeaf\Base\App\Repo\Contracts;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @template TModel
 * @template TResource of JsonResource<TModel>
 * @template TResourceCollection of ResourceCollection<TResource>
 * @extends CoreRepoInterface<TModel,TResource,TResourceCollection>
 */
interface BaseRepoInterface extends CoreRepoInterface
{
    /**
     * @param array $data
     * @return TResource
     */
    public function create(array $data);

    /**
     * @return TResourceCollection
     */
    public function all();

    /**
     * @return TResourceCollection
     */
    public function paginate(?int $perPage = null, ?int $page = null);

    /**
     * @return TResourceCollection
     */
    public function lazy();
    public function search(?callable $builder = null): self;

    /**
     * @param mixed $id
     * @param array $data
     * @return TResource
     */
    public function update(mixed $id, array $data);

    /**
     * @param mixed $id
     * @param array $data
     * @return int
     */
    public function updateQuietly(mixed $id, array $data): int;

    /**
     * @param mixed $id
     * @return TResource
     */
    public function delete(mixed $id);

    /**
     * @param mixed $id
     * @return int
     */
    public function deleteQuietly(mixed $id): int;

    /**
     * @param mixed $id
     * @return TResource
     */
    public function restore(mixed $id);

    /**
     * @param mixed $id
     * @return int
     */
    public function restoreQuietly(mixed $id): int;

    /**
     * @param mixed $id
     * @return TResource
     */
    public function forceDelete(mixed $id);

    /**
     * @param mixed $id
     * @return int
     */
    public function forceDeleteQuietly(mixed $id): int;

}
