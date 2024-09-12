<?php

namespace Callmeaf\Base\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphOne;

interface HasMeta
{
    public function meta(): MorphOne;
    public function metaData(): ?array;
}
