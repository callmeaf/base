<?php

return [
    \Callmeaf\Base\App\Enums\RequestType::API->value => [
        'prefix' => 'api',
        'as' => 'api.',
        'middleware' => ['api'],
        'version' => 'v1',
        'append_version_to_prefix' => true,
        'revalidate' => '1'
    ],
    \Callmeaf\Base\App\Enums\RequestType::WEB->value => [
        'prefix' => '',
        'as' => '',
        'middleware' => [],
        'version' => 'v1',
        'append_version_to_prefix' => false,
        'revalidate' => '1'
    ],
    \Callmeaf\Base\App\Enums\RequestType::ADMIN->value => [
        'prefix' => 'admin',
        'as' => 'admin.',
        'middleware' => ['admin', 'api'],
        'version' => 'v1',
        'append_version_to_prefix' => true,
        'revalidate' => '1'
    ],
    'facades' => [
        [
            'alias' => 'Base',
            'service' => \Callmeaf\Base\App\Services\BaseService::class,
            'facade' => \Callmeaf\Base\App\Facades\BaseFacade::class,
        ],
    ],
    'locales' => [
        'fa' => [
            'dir' => 'rtl'
        ],
        'en' => [
            'dir' => 'ltr'
        ]
    ],
    'page_key' => 'page',
    'per_page_key' => 'per_page',
    'max_per_page' => 300,
    'search_value_format' => '%%%s%%',
    'export_chunk_size' => 200,
    'export_rate_limit_request' => 5, // per minute for send export request ( in throttle middleware )
    'import_chunk_size' => 200,
    'import_rate_limit_request' => 5, // per minute for send import request ( in throttle middleware )
    'trashed_key' => 'trashed', // set ?trashed=true in url for get trashed data
    'restrict_route_middleware_key' => env('RESTRICT_ROUTE_KEY','4f928efb-4773-487b-a04f-c148c29f36dc'), // use in RestrictRouteMiddleware for security some routes,
    'relation_morph_map' => \Callmeaf\Base\App\Services\RelationMorphMap::class, // can use normal array key as alias value as repo or model ex: ['user' => UserRepo::class or User::class],
];
