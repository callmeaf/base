<?php

return [
    'api' => [
        'prefix_url' => 'api/af/v1',
        'prefix_route_name' => 'api.af.v1.',
        'middlewares' => [
            'api',
        ],
    ],
    'web' => [
        'prefix_url' => 'af/v1',
        'prefix_route_name' => 'af.v1.',
        'middlewares' => [
            'web'
        ],
    ],
];
