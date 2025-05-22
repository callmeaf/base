<?php

namespace Callmeaf\Base\App\Repo\V1;


use Callmeaf\Base\App\Repo\Contracts\CoreRepoInterface;
use Callmeaf\Base\App\Repo\Contracts\MediaMethodsInterface;
use Callmeaf\Base\App\Traits\Repo\CoreRepoMethods;
use Callmeaf\Base\App\Traits\Repo\MediaMethods;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @template TModel
 * @template TResource of JsonResource<TModel>
 * @template TResourceCollection of ResourceCollection<TResource>
 * @implements CoreRepoInterface<TModel>
 */
abstract class CoreRepo implements CoreRepoInterface,MediaMethodsInterface
{
    use CoreRepoMethods;
    use MediaMethods;

    /**
     * @var Builder<TModel>
     */
    private Builder $query;

    public readonly array $config;

    /**
     * @param class-string<TModel> $model
     */
    public function __construct(protected string $model)
    {
        $this->config = $this->model::config();
        $this->query = $this->model::query();
    }
}
