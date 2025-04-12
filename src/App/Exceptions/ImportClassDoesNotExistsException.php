<?php

namespace Callmeaf\Base\App\Exceptions;

use Exception;
use Illuminate\Http\Request;

class ImportClassDoesNotExistsException extends Exception
{
    public function __construct(protected string $type, string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function render(Request $request): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'message' => __('callmeaf-base::errors.import_class_does_not_exists', ['type' => $this->type])
        ], \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN);
    }
}
