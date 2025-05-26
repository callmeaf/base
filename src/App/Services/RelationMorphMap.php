<?php

namespace Callmeaf\Base\App\Services;

use Callmeaf\User\App\Repo\Contracts\UserRepoInterface;

class RelationMorphMap
{
    public function __invoke(): array
    {
        $userRepo = app(UserRepoInterface::class);
        return [
            'user' => $userRepo->getModel()::class,
        ];
    }
}
