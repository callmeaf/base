<?php

return [
    'api' => [
        'prefix_url' => '{locale}/api/callmeaf/v1',
        'prefix_route_name' => 'api.callmeaf.v1.',
        'middlewares' => [
            'api',
        ],
        'controller' => \Callmeaf\Base\Http\Controllers\V1\Api\ApiController::class,
    ],
    'web' => [
        'prefix_url' => '{locale}/callmeaf/v1',
        'prefix_route_name' => 'callmeaf.v1.',
        'middlewares' => [
            'web'
        ],
        'controller' => \Callmeaf\Base\Http\Controllers\V1\Web\WebController::class,
    ],
    'searcher' => \Callmeaf\Base\Utilities\V1\Searcher::class,
    'searcher_like_symbol' => '%', // % or %%
    'default_searcher_validation' => [
        'status' => [],
        'type' => [],
        'created_from' => [],
        'created_to' => [],
        'updated_from' => [],
        'updated_to' => [],
        'deleted_from' => [],
        'deleted_to' => [],
    ],
    'always_paginated' => true,
    'default_page' => 1,
    'default_per_page' => 15,
    'page_key' => 'page',
    'per_page_key' => 'per_page',
    'route_model_binding_key_for_authenticate_user' => '__auth',
];
