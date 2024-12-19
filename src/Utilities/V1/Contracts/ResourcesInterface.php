<?php

namespace Callmeaf\Base\Utilities\V1\Contracts;

use Callmeaf\Base\Utilities\V1\Resources;

interface ResourcesInterface
{
    public function all(?string $key = null): mixed;
    public function relations(): array;
    public function attributes(): array;
    public function columns(): array;
    public function idColumn(): string;
    public function index(): Resources;
    public function create(): Resources;
    public function store(): Resources;
    public function show(): Resources;
    public function edit(): Resources;
    public function update(): Resources;
    public function statusUpdate(): Resources;
    public function destroy(): Resources;
    public function trashed(): Resources;
    public function restore(): Resources;
    public function forceDestroy(): Resources;
}
