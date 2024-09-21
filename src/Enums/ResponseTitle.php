<?php

namespace Callmeaf\Base\Enums;

enum ResponseTitle: string
{
    case INDEX = 'index';
    case STORE = 'store';
    case SHOW = 'show';
    case UPDATE = 'update';
    case STATUS_UPDATE = 'status_update';
    case DESTROY = 'destroy';
    case RESTORE = 'restore';
    case TRASHED = 'trashed';
    case FORCE_DESTROY = 'force_destroy';
}
