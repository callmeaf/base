<?php

namespace Callmeaf\Base\App\Repo\V2;

use Callmeaf\Base\App\Repo\Contracts\BaseRepoInterface;
use Callmeaf\Base\App\Traits\BaseRepoMethods;

abstract class BaseRepo extends CoreRepo implements BaseRepoInterface
{
   use BaseRepoMethods;
}
