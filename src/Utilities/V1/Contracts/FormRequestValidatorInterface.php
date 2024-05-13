<?php

namespace Callmeaf\Base\Utilities\V1\Contracts;

interface FormRequestValidatorInterface
{
    public function index(): array;
    public function create(): array;
    public function store(): array;
    public function show(): array;
    public function edit(): array;
    public function update(): array;
    public function statusUpdate(): array;
    public function destroy(): array;
    public function trashed(): array;
    public function restore(): array;
    public function forceDestroy(): array;
}
