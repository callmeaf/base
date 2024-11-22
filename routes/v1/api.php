<?php

use Illuminate\Support\Facades\Route;

Route::prefix(config('callmeaf-base.api.prefix_url'))->as(config('callmeaf-base.api.prefix_route_name'))->middleware(config('callmeaf-base.api.middlewares'))->controller(config('callmeaf-base.api.controller'))->group(function() {
    Route::get('enums','getEnums');
});


