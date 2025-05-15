<?php

namespace Callmeaf\Base\App\Models;

use Callmeaf\Base\App\Models\Contracts\BaseConfigurable;
use Callmeaf\Base\App\Traits\Model\BaseModelMethods;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

abstract class BaseMediaModel extends Media implements BaseConfigurable
{
    use BaseModelMethods;
}
