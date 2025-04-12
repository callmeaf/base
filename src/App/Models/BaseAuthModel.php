<?php

namespace Callmeaf\Base\App\Models;

use Callmeaf\Base\App\Models\Contracts\BaseConfigurable;
use Callmeaf\Base\App\Traits\Model\BaseModelMethods;
use Illuminate\Foundation\Auth\User as Authenticatable;

abstract class BaseAuthModel extends Authenticatable implements BaseConfigurable
{
    use BaseModelMethods;
}
