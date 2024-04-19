<?php

namespace Callmeaf\Base\Contracts;

interface HasResponseTitles
{
    public function responseTitles(string $key): string;
}
