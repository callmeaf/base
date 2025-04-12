<?php

namespace Callmeaf\Base\App\Models;

use Callmeaf\Base\App\Models\Contracts\BaseConfigurable;
use Callmeaf\Base\App\Traits\Model\BaseModelMethods;
use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model implements BaseConfigurable
{
    use BaseModelMethods;
}
