<?php

namespace Callmeaf\Base\App\Services;

use Callmeaf\Auth\App\Repo\Contracts\AuthRepoInterface;
use Callmeaf\Product\App\Repo\Contracts\ProductRepoInterface;
use Callmeaf\User\App\Repo\Contracts\UserRepoInterface;

class RelationMorphMap
{
    public function __invoke(): array
    {
        $authRepo = app(AuthRepoInterface::class);
        $userRepo = app(UserRepoInterface::class);
        $productRepo = app(ProductRepoInterface::class);
        return [
            'auth' => $authRepo->getModel()::class,
            'user' => $userRepo->getModel()::class,
            'product' => $productRepo->getModel()::class,
        ];
    }
}
