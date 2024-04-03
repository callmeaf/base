<?php

namespace Callmeaf\Base\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class MustInstanceOfException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message ?: __('callmeaf-base::v1.errors.must_instance_if', ['target' => '', 'source' => '']), $code ?: Response::HTTP_INTERNAL_SERVER_ERROR, $previous);
    }
}

