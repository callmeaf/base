<?php

namespace Callmeaf\Base\Utilities\V1;

use Callmeaf\Base\Utilities\V1\Contracts\ResourcesInterface;

abstract class Resources implements ResourcesInterface
{
    protected array $data = [];
    public function all(?string $key = null): mixed
    {
        return $key
            ? $this->data[$key]
            : $this->data;
    }

    public function relations(): array
    {
        return $this->all('relations');
    }

    public function attributes(): array
    {
        return $this->all('attributes');
    }

    public function columns(): array
    {
        return $this->all('columns');
    }

    public function idColumn(): string
    {
        return $this->all('id_column');
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

