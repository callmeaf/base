<?php

namespace Callmeaf\Base\App\Facades;

use Callmeaf\Base\App\Services\BaseService;
use Illuminate\Support\Facades\Facade;

/**
 * @mixin BaseService
 */
class BaseFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return BaseService::class;
    }
}
