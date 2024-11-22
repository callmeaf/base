<?php

namespace Callmeaf\Base\Http\Controllers\V1\Api;

use Callmeaf\Base\Http\Controllers\BaseController;
use Callmeaf\Base\Http\Requests\V1\Api\EnumsRequest;

class ApiController extends BaseController
{
    public function getEnums(EnumsRequest $request)
    {
        try {
             return apiResponse($this->enums($request->query('key')),__('callmeaf-base::v1.successful_loaded'));
        } catch (\Exception $exception) {
            report($exception);
            return apiResponse([],$exception);
        }
    }
}
