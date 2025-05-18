<?php

namespace Callmeaf\Base\App\Models;

use Callmeaf\Base\App\Models\Contracts\BaseConfigurable;
use Callmeaf\Base\App\Traits\Model\BaseModelMethods;
use Spatie\Activitylog\Models\Activity;

abstract class BaseLogModel extends Activity implements BaseConfigurable
{
    use BaseModelMethods;
}
