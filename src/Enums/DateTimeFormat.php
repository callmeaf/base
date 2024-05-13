<?php

namespace Callmeaf\Base\Enums;

enum DateTimeFormat: string
{
    case DATE_TIME_WITH_DASH = 'Y-m-d';
    case DATE_TIME_WITH_DASH_AND_TIME_WITH_DOUBLE_POINT = 'Y-m-d H:i:s';
}
