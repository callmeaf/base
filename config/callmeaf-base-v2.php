<?php

return [
    \Callmeaf\Base\App\Enums\RequestType::API->value => [
        'prefix' => 'api',
        'as' => 'api.',
        'middleware' => ['api'],
        'version' => 'v2',
        'append_version_to_prefix' => true,
    ],
    \Callmeaf\Base\App\Enums\RequestType::WEB->value => [
        'prefix' => '',
        'as' => '',
        'middleware' => [],
        'version' => 'v2',
        'append_version_to_prefix' => true,
    ],
    \Callmeaf\Base\App\Enums\RequestType::ADMIN->value => [
        'prefix' => 'admin',
        'as' => 'admin.',
        'middleware' => ['admin'],
        'version' => 'v2',
        'append_version_to_prefix' => true,
    ],
    'facades' => [
        [
            'alias' => 'Base',
            'service' => \Callmeaf\Base\App\Services\BaseService::class,
            'facade' => \Callmeaf\Base\App\Facades\BaseFacade::class,
        ],
    ],
    'locales' => [
        'fa',
        'en',
    ],
    'page_key' => 'page',
    'per_page_key' => 'per_page',
    'search_value_format' => '%%%s%%',
    'export_chunk_size' => 200,
    'export_rate_limit_request' => 5, // per minute for send export request ( in throttle middleware )
    'import_chunk_size' => 200,
    'import_rate_limit_request' => 5, // per minute for send import request ( in throttle middleware )
    'trashed_key' => 'trashed', // set ?trashed=true in url for get trashed data
];
