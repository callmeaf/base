<?php

namespace Callmeaf\Base\Utilities\V1;

use Callmeaf\Base\Utilities\V1\Contracts\FormRequestValidatorInterface;
use Illuminate\Http\Request;

abstract class FormRequestValidator implements FormRequestValidatorInterface
{
    public function __construct(protected Request $request)
    {
    }


    public function index(): array
    {
        return [];
    }

    public function create(): array
    {
        return [];
    }

    public function store(): array
    {
        return [];
    }

    public function show(): array
    {
        return [];
    }

    public function edit(): array
    {
        return [];
    }

    public function update(): array
    {
        return [];
    }

    public function statusUpdate(): array
    {
        return [];
    }

    public function destroy(): array
    {
        return [];
    }

    public function trashed(): array
    {
        return [];
    }

    public function restore(): array
    {
        return [];
    }

    public function forceDestroy(): array
    {
        return [];
    }
}

