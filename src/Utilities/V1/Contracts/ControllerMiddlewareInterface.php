<?php

namespace Callmeaf\Base\Utilities\V1\Contracts;

interface ControllerMiddlewareInterface
{
    public function __invoke(): array;
}
