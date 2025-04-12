<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::controller(\Callmeaf\Base\App\Http\Controllers\Admin\V1\AdminController::class)->group(function () {
    Route::get('enums', 'enums');
    Route::get('revalidate', 'revalidate');
});

