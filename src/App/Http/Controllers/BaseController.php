<?php

namespace Callmeaf\Base\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController extends Controller
{
    protected FormRequest $request;
    public function __construct(?array $config = null)
    {
        if (empty($config)) {
            return;
        }

        $this->handleRequest(requests: $config['requests'] ?? []);
    }

    public function enums(Request $request)
    {
        if (! needToRevalidate()) {
            return response()->json(null, Response::HTTP_NOT_MODIFIED);
        }
        return response()->json(\Base::enums(package: $request->query('package')));
    }

    public function revalidate()
    {
        $isProduction = app()->isProduction();
        $cookie = cookie('x_revalidate', \Base::revalidate(), 60, '/', getCookieDomainFromAppUrl(), $isProduction, $isProduction, false, 'lax');

        return response()->json()->cookie($cookie);
    }

    private function handleRequest(array $requests): void
    {
        if (empty($requests)) {
            return;
        }

        $request = @$requests[requestType()][requestActionName()] ?? \Base::getFormRequestByActionName();
        if ($request) {
            $request = app($request);
            $this->request = $request;

            $this->authorizeRequest(request: $request);
            $this->validateRequest(request: $request);
        }
    }

    private function authorizeRequest(FormRequest $request): bool
    {
        return $request->authorize();
    }

    private function validateRequest(FormRequest $request): void
    {
        $request->validateResolved();
    }


}
