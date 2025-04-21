<?php

namespace Callmeaf\Base\App\Http\Controllers\Web\V2;

use Callmeaf\Base\App\Http\Controllers\BaseController;

abstract class WebController extends BaseController
{
    public function __construct(?array $config = null)
    {
        parent::__construct($config);
    }
}
