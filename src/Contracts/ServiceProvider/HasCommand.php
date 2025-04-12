<?php

namespace Callmeaf\Base\Contracts\ServiceProvider;

interface HasCommand
{
    public function commandsClass(): array;
}
