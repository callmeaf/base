<?php

namespace Callmeaf\Base\App\Enums;

enum EventNameSuffix: string
{
    case Indexed = 'all';
    case PAGINATED = 'paginate';
    case Created = 'create';
    case Showed = 'findBy';
    case Updated = 'update';
    case Deleted = 'delete';
    case Restored = 'restore';
    case Trashed = 'trashed';
    case ForceDeleted = 'forceDelete';
}
