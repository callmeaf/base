<?php

namespace Callmeaf\Base\Utilities\V1;

use Callmeaf\Auth\Utilities\V1\Register\Api\RegisterResources;
use Callmeaf\Base\Utilities\V1\Contracts\ResourcesInterface;

abstract class Resources implements ResourcesInterface
{
    protected array $data = [];
    public function all(): array
    {
        return $this->data;
    }

    public function relations(): array
    {
        return $this->data['relations'];
    }

    public function attributes(): array
    {
        return $this->data['attributes'];
    }

    public function columns(): array
    {
        return $this->data['columns'];
    }

    public function idColumn(): string
    {
        return $this->data['id_column'];
    }

    public function index(): self
    {
        return $this;
    }

    public function create(): self
    {
        return $this;
    }

    public function store(): self
    {
        return $this;
    }

    public function show(): self
    {
        return $this;
    }

    public function edit(): self
    {
        return $this;
    }

    public function update(): self
    {
        return $this;
    }

    public function statusUpdate(): self
    {
        return $this;
    }

    public function destroy(): self
    {
        return $this;
    }

    public function trashed(): self
    {
        return $this;
    }

    public function restore(): self
    {
        return $this;
    }

    public function forceDestroy(): self
    {
        return $this;
    }
}

