<?php

namespace Callmeaf\Base\App\Services;

class Importer
{
    protected int $total = 0;
    protected int $success = 0;

    public function getSuccess(): int
    {
        return $this->success;
    }

    public function getTotal(): int
    {
        return $this->total;
    }
}
