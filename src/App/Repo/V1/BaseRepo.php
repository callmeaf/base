<?php

namespace Callmeaf\Base\App\Repo\V1;

use Callmeaf\Base\App\Repo\Contracts\BaseRepoInterface;
use Callmeaf\Base\App\Repo\Contracts\MediaMethodsInterface;
use Callmeaf\Base\App\Traits\Repo\BaseRepoMethods;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @template TModel
 * @template TResource of JsonResource<TModel>
 * @template TResourceCollection of ResourceCollection<TResource>
 * @implements BaseRepoInterface<TModel>
 * @extends CoreRepo<TModel,TResource,TResourceCollection>
 */
abstract class BaseRepo extends CoreRepo implements BaseRepoInterface,MediaMethodsInterface
{
    use BaseRepoMethods;
}
