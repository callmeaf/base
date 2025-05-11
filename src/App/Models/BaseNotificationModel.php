<?php

namespace Callmeaf\Base\App\Models;

use Callmeaf\Base\App\Models\Contracts\BaseConfigurable;
use Callmeaf\Base\App\Traits\Model\BaseModelMethods;
use Illuminate\Notifications\DatabaseNotification;

abstract class BaseNotificationModel extends DatabaseNotification implements BaseConfigurable
{
    use BaseModelMethods;
}
