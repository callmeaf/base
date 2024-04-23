<?php

namespace Callmeaf\Base\Utilities\V1;


use Callmeaf\Base\Utilities\V1\Contracts\FormRequestAuthorizerInterface;
use Illuminate\Http\Request;

abstract class FormRequestAuthorizer implements FormRequestAuthorizerInterface
{
    public function __construct(protected Request $request)
    {
    }

    public function index(): bool
    {
        return false;
    }

    public function create(): bool
    {
        return false;
    }

    public function store(): bool
    {
        return false;
    }

    public function show(): bool
    {
        return false;
    }

    public function edit(): bool
    {
        return false;
    }

    public function update(): bool
    {
        return false;
    }

    public function destroy(): bool
    {
        return false;
    }

    public function trashed(): bool
    {
        return false;
    }

    public function restore(): bool
    {
        return false;
    }

    public function forceDestroy(): bool
    {
        return false;
    }
}
