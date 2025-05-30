<?php

namespace Callmeaf\Base\App\Services;


use Callmeaf\Base\App\Constants\BaseConstant;
use Callmeaf\Base\App\Enums\ImportType;
use Callmeaf\Base\App\Enums\RandomType;
use Callmeaf\Base\App\Enums\RequestType;
use Callmeaf\Base\App\Models\Contracts\BaseConfigurable;
use Callmeaf\Base\App\Repo\Contracts\CoreRepoInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;

class BaseService
{
    public function config(?string $key = null): mixed
    {
        return baseConfig(key: $key);
    }
    public function apiConfig(?string $key = null): mixed
    {
        return $this->config(key: "api.{$key}");
    }

    public function apiPrefix(): string
    {
        $prefix = $this->apiConfig(key: 'prefix');
        return $this->appendApiVersionToPrefix() ? $prefix . '/' . $this->apiVersion() : $prefix;
    }

    public function apiVersion(): string
    {
        return $this->apiConfig(key: 'version');
    }

    public function apiMiddleware(): array|string
    {
        return $this->apiConfig(key: 'middleware');
    }

    public function apiAs(): array|string
    {
        return $this->apiConfig(key: 'as');
    }

    public function appendApiVersionToPrefix(): bool
    {
        return $this->apiConfig(key: 'append_version_to_prefix');
    }
    public function webConfig(?string $key = null): array|string
    {
        $webConfig = $this->config(key: 'web');
        return $key ? $webConfig[$key] : $webConfig;
    }

    public function webPrefix(): string
    {
        $prefix = $this->webConfig(key: 'prefix');
        return $this->appendWebVersionToPrefix() ? $prefix . '/' . $this->webVersion() : $prefix;
    }

    public function webVersion(): string
    {
        return $this->webConfig(key: 'version');
    }

    public function webMiddleware(): array|string
    {
        return $this->webConfig(key: 'middleware');
    }

    public function webAs(): string
    {
        return $this->webConfig(key: 'as');
    }

    public function appendWebVersionToPrefix(): bool
    {
        return $this->webConfig(key: 'append_version_to_prefix');
    }

    public function adminConfig(?string $key = null): array|string
    {
        $adminConfig = $this->config(key: 'admin');
        return $key ? $adminConfig[$key] : $adminConfig;
    }

    public function adminPrefix(): string
    {
        $prefix = $this->adminConfig(key: 'prefix');
        return $this->appendAdminVersionToPrefix() ? $prefix . '/' . $this->adminVersion() : $prefix;
    }

    public function adminVersion(): string
    {
        return $this->adminConfig(key: 'version');
    }

    public function adminMiddleware(): array|string
    {
        return $this->adminConfig(key: 'middleware');
    }

    public function adminAs(): string
    {
        return $this->adminConfig(key: 'as');
    }

    public function appendAdminVersionToPrefix(): bool
    {
        return $this->adminConfig(key: 'append_version_to_prefix');
    }

    public function toResource(string $resource, BaseConfigurable|MissingValue|null $model): null|JsonResource
    {
        if(is_null($model)) {
            return null;
        }
        return new $resource($model);
    }

    public function toResourceCollection(string $resourceCollection, Collection|LengthAwarePaginator|LazyCollection|MissingValue $collection): ResourceCollection
    {
        return new $resourceCollection($collection);
    }

    public function getConfigFromModel(string $model): array
    {
        return $model::config();
    }

    public function getConfigFromPackageName(string $packageName): array
    {
        $packageName = str($packageName)->camel()->kebab()->toString();
        return config("callmeaf-{$packageName}-" . requestVersion()) ?? [];
    }

    public function getConfigFromRepo(string $repo): array
    {
        /**
         * @var CoreRepoInterface $repo
         */
        $repo = app($repo);
        return $repo->config;
    }

    public function getRouteConfigFromRepo(string $repo): array
    {
        $config = $this->getConfigFromRepo(repo: $repo);
        $requestType = requestType();

        if (! $requestType) {
            $callerFile = debug_backtrace(options: DEBUG_BACKTRACE_IGNORE_ARGS, limit: 2)[1]['file'];
            $callerDir = str(dirname($callerFile));

            $requestType = RequestType::tryFrom($callerDir->afterLast(DIRECTORY_SEPARATOR)->toString())?->value;
        }

        $controller = $config['controllers'][$requestType];
        $route = $config['routes'][$requestType];
        $prefix = $route['prefix'];
        $as = $route['as'];
        $middleware = $route['middleware'];

        return [
            $controller,
            $prefix,
            $as,
            $middleware,
        ];
    }

    public function getPackageNameFromModel(string $model): string
    {
        return str($model::configKey())->replace(['callmeaf-', 'v1', '.php'], '')->studly()->toString();
    }

    public function random(int $length, RandomType $type = RandomType::MIXED): string
    {
        return match ($type) {
            RandomType::NUMBER => substr(str_shuffle(string: '0123456789'), offset: 0, length: $length),
            default => Str::random(length: $length),
        };
    }

    public function searchValue(string $value): string
    {
        return sprintf($this->config('search_value_format'), $value);
    }

    public function enums(?string $package = null): array
    {
        return once(function () use ($package) {
            $allConfig = $this->getAllPackagesConfig();
            $enumsConfig = [];
            foreach ($allConfig as $packageName => $config) {
                $enumsConfig[$packageName] = $config['enums'] ?? [];
            }

            $data = [];
            foreach ($enumsConfig as $packageName => $enums) {
                $item = [];
                $packageNameSnakeCase = str($packageName)->camel()->camel()->snake()->toString();

                foreach ($enums as $key => $enum) {
                    foreach ($enum::cases() as $case) {
                        $item[str($key)->plural()->toString()][$case->value] = $this->enumCaseTranslator($packageName, $case);
                    }
                }
                $data[$packageNameSnakeCase] = $item;
            }

            return $package ? $data[$package] : $data;
        });
    }

    public function enumsLang(string $package): array
    {
        return __("callmeaf-{$package}::enums") ?? [];
    }

    public function enumCaseTranslator(string $package, $case): string
    {
        if (empty($case)) {
            return '';
        }

        $package = str($package)->camel()->kebab()->toString();

        return $this->enumsLang(package: $package)[get_class($case)][$case->name] ?? '';
    }

    public function revalidate(): string
    {
        return str($this->config(key: \requestType())['revalidate'] ?? '')->toString();
    }

    public function getFormRequestByActionName(): string
    {
        $request = request();
        $actionName = requestActionName(request: $request, onlyControllerMethod: false);
        $controllerMethod = str(
            requestActionName(request: $request)
        )->studly()->toString();

        $type = str(\requestType(request: $request))->ucfirst()->toString();
        $version = str(requestVersion(request: $request))->ucfirst()->toString();
        $packageName = str($actionName)->between('Callmeaf', 'App')->replace(['/', '\\'], '')->toString();
        // form request path
        return str($actionName)->before('Controllers')->append("Requests\\$type\\$version\\{$packageName}{$controllerMethod}Request")->toString();
    }

    public function exportFileName(string $model, string $extension): string
    {
        $packageName = $this->getPackageNameFromModel(model: $model);
        $packageName = str($packageName)->plural()->lower()->toString();

        return str(
            now()->format('Y_m_d_H_i_s'),
        )->append(
            '_',
            $packageName,
            '.',
            $this->random(length: 10),
            '.',
            str($extension)->lower()->toString(),
        );
    }

    public function importFileName(string $model, string $extension): string
    {
        $packageName = $this->getPackageNameFromModel(model: $model);
        $packageName = str($packageName)->plural()->lower()->toString();

        return str(
            now()->format('Y_m_d_H_i_s'),
        )->append(
            '_',
            $packageName,
            '.',
            $this->random(length: 10),
            '.',
            str($extension)->lower()->toString(),
        );
    }

    public function mimesImportValidation(ImportType $type): string
    {
        return match ($type) {
            ImportType::EXCEL => 'xlsx,xls',
            default => '',
        };
    }

    public function getAllPagesData(): bool
    {
        return request()->query($this->config('page_key')) === BaseConstant::ALL;
    }

    public function getTrashedData(): bool
    {
        return request()->query($this->config('trashed_key')) == 'true';
    }

    public function classUse(string $className,string $targetName): bool
    {
        return in_array($targetName,class_uses($className));
    }

    public function getAllPackagesConfig(): array
    {
        $files = app(Filesystem::class);

        $paths = [
            packagePath(''),
            packagePath('',getVendorPath: true),
        ];

        $allConfig = [];

        foreach ($paths as $path) {
            if($files->isDirectory($path)) {
                foreach ($files->directories($path) as $directory) {
                    $packageName = str($directory)->afterLast(DIRECTORY_SEPARATOR)->camel()->kebab()->toString();
                    $allConfig[$packageName] = $this->getConfigFromPackageName(packageName: $packageName);
                }
            }
        }

        return array_filter($allConfig);
    }

    public function currentLangConfig(): array
    {
        $currentLocale = App::currentLocale();
        $langConfig = $this->config(key: 'locales')[App::currentLocale()] ?? null;

        if(empty($langConfig)) {
            $version = requestVersion();
            throw new \Exception("$currentLocale has no config in callmeaf-base-$version.php");
        }

        return $langConfig;
    }

    public function currentLangDir(): string
    {
        return $this->currentLangConfig()['dir'];
    }

    public function relationMorphMap(): array
    {
        $relationMorphMapClass = $this->config('relation_morph_map');

        if(empty($relationMorphMapClass)) {
            return [];
        }

        if(is_array($relationMorphMapClass)) {
            foreach ($relationMorphMapClass as $alias => $class) {
                $model = app($class);
                if($model instanceof CoreRepoInterface) {
                    $model = $model->getModel()::class;
                }
                $relationMorphMapClass[$alias] = $model;
            }
            return $relationMorphMapClass;
        }

        return (new $relationMorphMapClass)();
    }

    public function relationMorphMapAlias(): array
    {
        return array_keys($this->relationMorphMap());
    }

    public function getMorphedModel(string $morphType): ?string
    {
        return $this->relationMorphMap()[$morphType] ?? null;
    }
}
