<?php

namespace Callmeaf\Base\App\Models;

use Callmeaf\Base\App\Traits\Model\BasePivotModelMethods;
use Illuminate\Database\Eloquent\Relations\Pivot;

class BasePivotModel extends Pivot
{
    use BasePivotModelMethods;
}
