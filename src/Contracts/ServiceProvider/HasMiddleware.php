<?php

namespace Callmeaf\Base\Contracts\ServiceProvider;

use Illuminate\Routing\Router;

interface HasMiddleware
{
    public function middlewares(Router $router): void;
}
