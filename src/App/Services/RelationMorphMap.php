<?php

namespace Callmeaf\Base\App\Services;

use Callmeaf\Product\App\Repo\Contracts\ProductRepoInterface;
use Callmeaf\User\App\Repo\Contracts\UserRepoInterface;

class RelationMorphMap
{
    public function __invoke(): array
    {
        $userRepo = app(UserRepoInterface::class);
        $productRepo = app(ProductRepoInterface::class);
        return [
            'user' => $userRepo->getModel()::class,
            'product' => $productRepo->getModel()::class,
        ];
    }
}
