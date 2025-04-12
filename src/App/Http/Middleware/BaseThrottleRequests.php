<?php

namespace Callmeaf\Base\App\Http\Middleware;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Routing\Middleware\ThrottleRequests;

class BaseThrottleRequests extends ThrottleRequests
{
    protected function buildException($request, $key, $maxAttempts, $responseCallback = null): HttpResponseException|ThrottleRequestsException
    {
        $retryAfter = $this->getTimeUntilNextRetry($key);

        $headers = $this->getHeaders(
            $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts, $retryAfter),
            $retryAfter
        );

        return is_callable($responseCallback)
            ? new HttpResponseException($responseCallback($request, $headers))
            : new ThrottleRequestsException(__('callmeaf-base::middlewares.custom_throttle.message', ['seconds' => $retryAfter]), null, $headers);
    }
}
