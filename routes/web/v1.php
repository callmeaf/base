<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::controller(\Callmeaf\Base\App\Http\Controllers\Web\V1\WebController::class)->group(function() {
    Route::get('enums', 'enums');
    Route::get('revalidate','revalidate');
});

