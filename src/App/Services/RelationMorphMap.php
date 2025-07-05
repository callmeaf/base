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
        /**
         * @var AuthRepoInterface $authRepo
         */
        $authRepo = app(AuthRepoInterface::class);
        $authModel = $authRepo->getModel();
        /**
         * @var UserRepoInterface $userRepo
         */
        $userRepo = app(UserRepoInterface::class);
        $userModel = $userRepo->getModel();
        /**
         * @var ProductCategoryRepoInterface $productCategoryRepo
         */
        $productCategoryRepo = app(ProductCategoryRepoInterface::class);
        $productCategoryModel = $productCategoryRepo->getModel();
        /**
         * @var ProductRepoInterface $productRepo
         */
        $productRepo = app(ProductRepoInterface::class);
        $productModel = $productRepo->getModel();

        return [
            $authModel->relationMorphMapName() => $authModel::class,
            $userModel->relationMorphMapName() => $userModel::class,
            $productCategoryModel->relationMorphMapName() => $productCategoryModel::class,
            $productModel->relationMorphMapName() => $productModel::class,
        ];
    }
}
