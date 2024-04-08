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
    'searchable_columns' => [
        'status' => 'status',
        'type' => 'type',
        'created_from' => [
            'created_at',
            '>',
        ],
        'created_to' => [
            'created_at',
            '<',
        ],
        'updated_from' => [
            'updated_at',
            '>',
        ],
        'updated_to' => [
            'updated_at',
            '<',
        ],
        'deleted_from' => [
            'deleted_at',
            '>',
        ],
        'deleted_to' => [
            'deleted_at',
            '<',
        ],
    ],
];
