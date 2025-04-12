<?php

namespace Callmeaf\Base\App\Enums;

enum DateTimeFormat: string
{
    case DATE_TIME = 'Y-m-d H:i:s';
    case DATE = 'Y-m-d';
    case TIME = 'H:i:s';
}
