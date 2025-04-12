<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::controller(\Callmeaf\Base\App\Http\Controllers\Api\V1\ApiController::class)->group(function () {
    Route::get('enums', 'enums');
    Route::get('revalidate', 'revalidate');
});
