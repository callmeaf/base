<?php

use Callmeaf\Base\App\Enums\RequestType;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;

if (!function_exists('baseConfig')) {
    function baseConfig(string $key = ''): mixed
    {
        $version = requestVersion();
        $configKey = "callmeaf-base-{$version}";
        if ($key) {
            $configKey .= ".{$key}";
        }

        return config($configKey);
    }
}


if (!function_exists('requestType')) {
    function requestType(?Request $request = null): string
    {
        return once(fn() => match (true) {
            app()->runningInConsole() => config('app.package_request_type_in_console'),
            isApiRequest(request: $request) => RequestType::API->value,
            isWebRequest(request: $request) => RequestType::WEB->value,
            isAdminRequest(request: $request) => RequestType::ADMIN->value,
            default => '',
        });
    }
}

if (!function_exists('requestVersion')) {
    function requestVersion(?Request $request = null): string
    {
        return once(function () use ($request) {
            $version = 'v1';

            if (app()->runningInConsole()) {
                $version = config('app.package_version_in_console');
            } else {
                $request ??= request();
                // find version of app with request path
                if (preg_match('/\/(v\d+)\b/', $request->path(), $matches)) {
                    $version = $matches[1];
                }
            }

            logger('$version');
            logger($version);
            if (! in_array($version, allExistsVersion())) {
                throw new \Error("Version of {$version} for base does not exists");
            }
            // default version if not found from request path
            return $version;
        });
    }
}

if (! function_exists('requestActionName')) {
    function requestActionName(?Request $request = null, bool $onlyControllerMethod = true): string
    {
        $request ??= request();
        $actionName = $request->route()->getActionName();

        if (! $onlyControllerMethod) {
            return $actionName;
        }

        return str($actionName)->after('@')->toString();
    }
}

if (!function_exists('isApiRequest')) {
    function isApiRequest(?Request $request = null): bool
    {
        $request ??= request();
        $prefixApi = Base::apiPrefix();
        return $request->is("{$prefixApi}/*");
    }
}

if (!function_exists('isWebRequest')) {
    function isWebRequest(?Request $request = null): bool
    {
        $request ??= request();
        $prefixWeb = Base::webPrefix();
        return $request->is("{$prefixWeb}/*");
    }
}

if (!function_exists('isAdminRequest')) {
    function isAdminRequest(?Request $request = null): bool
    {
        $request ??= request();
        $prefixAdmin = Base::adminPrefix();
        return $request->is("{$prefixAdmin}/*");
    }
}

if (! function_exists('isPostmanRequest')) {
    function isPostmanRequest(?Request $request = null): bool
    {
        $request ??= request();
        return $request->hasHeader('postman-token');
    }
}
if (! function_exists('packagePath')) {
    function packagePath(string $package, string $path = ''): string
    {
        $vendorPath = base_path("vendor/callmeaf");
        if ($path) {
            $package .= "/$path";
        }

        if(is_dir("$vendorPath/$package")) {
            return "$vendorPath/$package";
        }

        return base_path("packages/$package");
    }
}

if (! function_exists('allExistsVersion')) {
    function allExistsVersion(string $package = 'base'): array
    {
        $files = app(Filesystem::class);
        $versions = [];
        foreach ($files->files(packagePath($package, 'config')) as $file) {
            $versions[] = str($file->getFilename())->afterLast('-')->replace('.php', '');
        }

        return $versions;
    }
}

if (! function_exists('needToRevalidate')) {
    function needToRevalidate(): bool
    {
        $request = request();
        return strval((baseConfig()[\requestType($request)]['revalidate'] ?? '')) !== strval($request->cookie('x_revalidate'));
    }
}
