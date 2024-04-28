<?php

namespace Callmeaf\Base\Utilities\V1\Contracts;

interface FormRequestAuthorizerInterface
{
    public function index(): bool;
    public function create(): bool;
    public function store(): bool;
    public function show(): bool;
    public function edit(): bool;
    public function update(): bool;
    public function statusUpdate(): bool;
    public function destroy(): bool;
    public function trashed(): bool;
    public function restore(): bool;
    public function forceDestroy(): bool;
}
