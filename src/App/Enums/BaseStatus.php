<?php

namespace Callmeaf\Base\App\Enums;

enum BaseStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case PENDING = 'pending';
}
