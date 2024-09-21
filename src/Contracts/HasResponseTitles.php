<?php

namespace Callmeaf\Base\Contracts;

use Callmeaf\Base\Enums\ResponseTitle;

interface HasResponseTitles
{
    public function responseTitles(ResponseTitle|string $key,string $default = ''): string;
}
