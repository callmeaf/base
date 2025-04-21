<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('enums', function (Request $request) {
    return response()->json(Base::enums($request->query('package')));
});
