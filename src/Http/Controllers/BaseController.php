<?php

namespace Callmeaf\Base\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BaseController extends Controller
{
    public function http(Request $request)
    {
        return Http::withToken(token: $request->bearerToken());
    }
}
