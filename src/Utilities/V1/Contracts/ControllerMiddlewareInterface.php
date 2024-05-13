<?php

namespace Callmeaf\Base\Utilities\V1\Contracts;

use Callmeaf\Base\Http\Controllers\BaseController;

interface ControllerMiddlewareInterface
{
    public function __invoke(BaseController $controller): void;
}
