<?php

namespace Callmeaf\Base\Contracts\ServiceProvider;

interface HasSeeder
{
    public function seeders(): array;
}
