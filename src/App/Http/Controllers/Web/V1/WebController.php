<?php

namespace Callmeaf\Base\App\Http\Controllers\Web\V1;

use Callmeaf\Base\App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

class WebController extends BaseController
{
    public function __construct(?array $config = null)
    {
        parent::__construct($config);
    }

    public function enums(Request $request)
    {
        return parent::enums($request);
    }

    public function revalidate()
    {
        return parent::revalidate();
    }
}
