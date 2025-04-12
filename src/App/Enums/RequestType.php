<?php

namespace Callmeaf\Base\App\Enums;

enum RequestType: string
{
    case API = 'api';
    case WEB = 'web';
    case ADMIN = 'admin';
}
