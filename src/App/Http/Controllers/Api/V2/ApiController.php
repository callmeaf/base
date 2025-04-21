<?php

namespace Callmeaf\Base\App\Http\Controllers\Api\V2;

use Callmeaf\Base\App\Http\Controllers\BaseController;

abstract class ApiController extends BaseController
{
    public function __construct(?array $config = null)
    {
        parent::__construct($config);
    }
}
