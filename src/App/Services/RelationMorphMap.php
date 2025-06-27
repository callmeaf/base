<?php

namespace Callmeaf\Base\App\Services;

use Callmeaf\Auth\App\Repo\Contracts\AuthRepoInterface;
use Callmeaf\Product\App\Repo\Contracts\ProductRepoInterface;
use Callmeaf\ProductCategory\App\Repo\Contracts\ProductCategoryRepoInterface;
use Callmeaf\User\App\Repo\Contracts\UserRepoInterface;

class RelationMorphMap
{
    public function __invoke(): array
    {
        $authRepo = app(AuthRepoInterface::class);
        $userRepo = app(UserRepoInterface::class);
        $productCategoryRepo = app(ProductCategoryRepoInterface::class);
        $productRepo = app(ProductRepoInterface::class);
        return [
            'auth' => $authRepo->getModel()::class,
            'user' => $userRepo->getModel()::class,
            'product_category' => $productCategoryRepo->getModel()::class,
            'product' => $productRepo->getModel()::class,
        ];
    }
}
