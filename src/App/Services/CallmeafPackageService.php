<?php

namespace Callmeaf\Base\App\Services;

use Callmeaf\Base\App\Enums\RequestType;
use Callmeaf\Base\App\Traits\Package\BasePackageMethods;
use Error;
use Illuminate\Filesystem\Filesystem;

class CallmeafPackageService
{
    use BasePackageMethods;

    private bool $remove = false;
    private string $errorType = 'make';
    private readonly bool $packageMade;
    private readonly Filesystem $files;
    private array $errors = [];

    public function __construct(private readonly string $packageName, private readonly string $version, private readonly array $guards)
    {
        $this->files = app(Filesystem::class);
    }

    public function packageDir(string $append = ''): string
    {
        $package = str($this->packageName)->snake()->toString();
        return packagePath($package, $append);
    }

    public function versionExists(): bool
    {
        $configName = str($this->packageName)->snake(delimiter: '-')->toString();

        return $this->checkFileExists(path: $this->packageDir(
            append: "config/callmeaf-{$configName}-{$this->version}.php"
        ));
    }

    public function makePackage(): self
    {
        $packageDir = $this->packageDir();
        $result = $this->mkdir($packageDir,true);

        if (! $result) {
            throw new Error("Failed to {$this->errorType} package $packageDir");
        }

        $this->packageMade = true;

        return $this;
    }

    public function removePackage(): self
    {
        $this->remove = true;
        $this->errorType = 'remove';

        $this->config();
        $this->repo();
        $this->resource();
        $this->event();
        $this->request();
        $this->controller();
        $this->route();
        $this->lang();
        $this->import();
        $this->export();

        return $this;
    }

    public function config(): self
    {
        $this->ensurePackageMade();

        $result = $this->mkdir(
            $this->packageDir(append: 'config')
        );

        if (! $result) {
            $this->pushError(message: "Failed to {$this->errorType} config folder");
            return $this;
        }

        $configName = str($this->packageName)->snake(delimiter: '-')->toString();
        $route = str($this->packageName)->plural()->snake()->toString();
        $version = str($this->version)->ucfirst()->toString();
        $controller = str($this->packageName)->singular()->snake()->lower()->toString();
        $pluralModel = str($this->packageName)->headline()->plural()->toString();
        $result = $this->mkfile(path: $this->packageDir(
            append: "config/callmeaf-{$configName}-{$this->version}.php"),
            contents: str_replace(['{{ $model }}', '{{ $route }}', '{{ $version }}', '{{ $controller }}','{{ $pluralModel }}'], [$this->packageName, $route, $version, $controller,$pluralModel], $this->stub(key: 'config'))
        );
        if (! $result) {
            $this->pushError(message: "Failed to {$this->errorType} config file {$configName}");
        }

        return $this;
    }

    public function model(): self
    {
        $this->ensurePackageMade();

        $result = $this->mkdir(
            path: $this->packageDir(append: 'src/App/Models'),
            recursive: true,
        );
        if (! $result) {
            $this->pushError(message: "Failed to {$this->errorType} models folder");
            return $this;
        }

        $modelName = $this->packageName;
        $config = str($this->packageName)->snake(delimiter: '-')->lower()->toString();

        $result = $this->mkfile(path: $this->packageDir(
            append: "src/App/Models/$modelName.php"),
            contents: str_replace(['{{ $model }}', '{{ $config }}'], [$modelName, $config], $this->stub(key: 'model'))
        );
        if (! $result) {
            $this->pushError(message: "Failed to {$this->errorType} model file {$modelName}");
        }

        return $this;
    }

    public function repo(): self
    {
        $this->ensurePackageMade();

        $result = $this->mkdir(
            path: $this->packageDir(append: "src/App/Repo/Contracts"),
            recursive: true,
        );
        if (! $result) {
            $this->pushError(message: "Failed to {$this->errorType} repo contracts folder");
            return $this;
        }

        $modelName = $this->packageName;
        $version = str($this->version)->ucfirst()->toString();
        $guard = array_values($this->guards)[0];
        $guard = str($guard)->ucfirst()->toString();

        $result = $this->mkfile(path: $this->packageDir(
            append: "src/App/Repo/Contracts/{$modelName}RepoInterface.php"),
            contents: str_replace(['{{ $model }}', '{{ $version }}', '{{ $guard }}'], [$modelName, $version, $guard], $this->stub(key: 'repo.interface'))
        );
        if (! $result) {
            $this->pushError(message: "Failed to {$this->errorType} repo interface file {$modelName}");
        }

        $result = $this->mkdir(
            path: $this->packageDir(append: "src/App/Repo/{$version}"),
            recursive: true,
        );
        if (! $result) {
            $this->pushError(message: "Failed to {$this->errorType} repo folder {$version}");
            return $this;
        }
        $result = $this->mkfile(path: $this->packageDir(
            append: "src/App/Repo/{$version}/{$modelName}Repo.php"),
            contents: str_replace(['{{ $model }}', '{{ $version }}'], [$modelName, $version], $this->stub(key: 'repo'))
        );
        if (! $result) {
            $this->pushError(message: "Failed to {$this->errorType} repo file {$modelName}");
        }

        return $this;
    }

    public function resource(): self
    {
        $this->ensurePackageMade();

        $version = str($this->version)->ucfirst()->toString();
        $modelName = $this->packageName;

        foreach ($this->guards as $guard) {
            $guard = str($guard)->ucfirst()->toString();
            $result = $this->mkdir(
                path: $this->packageDir(append: "src/App/Http/Resources/{$guard}/{$version}"),
                recursive: true,
            );
            if (! $result) {
                $this->pushError(message: "Failed to {$this->errorType} resources folder {$guard} {$version}");
                return $this;
            }

            $result = $this->mkfile(path: $this->packageDir(
                append: "src/App/Http/Resources/{$guard}/{$version}/{$modelName}Resource.php"),
                contents: str_replace(['{{ $model }}', '{{ $guard }}', '{{ $version }}'], [$modelName, $guard, $version], $this->stub(key: 'resource'))
            );
            if (! $result) {
                $this->pushError(message: "Failed to {$this->errorType} resource file {$guard} {$version}");
            }

            $result = $this->mkfile(path: $this->packageDir(
                append: "src/App/Http/Resources/{$guard}/{$version}/{$modelName}Collection.php"),
                contents: str_replace(['{{ $model }}', '{{ $guard }}', '{{ $version }}'], [$modelName, $guard, $version], $this->stub(key: 'resource.collection'))
            );
            if (! $result) {
                $this->pushError(message: "Failed to {$this->errorType} collection file {$guard} {$version}");
            }
        }

        return $this;
    }

    public function event(): self
    {
        $this->ensurePackageMade();

        $version = str($this->version)->ucfirst()->toString();
        $modelName = $this->packageName;

        $singularVar = str($modelName)->lower()->singular();
        $pluralVar = str($modelName)->lower()->plural();
        $lifeCycles = [
            'Indexed' => $pluralVar,
            'Created' => $singularVar,
            'Showed' => $singularVar,
            'Updated' => $singularVar,
            'Deleted' => $singularVar,
            'Trashed' => $pluralVar,
            'Restored' => $singularVar,
            'ForceDeleted' => $singularVar,
        ];

        foreach ($this->guards as $guard) {
            $guard = str($guard)->ucfirst()->toString();
            $result = $this->mkdir(
                path: $this->packageDir(append: "src/App/Events/{$guard}/{$version}"),
                recursive: true,
            );
            if (! $result) {
                $this->pushError(message: "Failed to {$this->errorType} events folder {$guard} {$version}");
                return $this;
            }

            $result = $this->mkdir(
                path: $this->packageDir(append: "src/App/Listeners/{$guard}/{$version}"),
                recursive: true,
            );
            if (! $result) {
                $this->pushError(message: "Failed to {$this->errorType} listeners folder {$guard} {$version}");
                return $this;
            }

            foreach ($lifeCycles as $lifeCycle => $var) {
                $stubKey = $var === $singularVar ? 'event.model' : 'event.collection';
                $result = $this->mkfile(path: $this->packageDir(
                    append: "src/App/Events/{$guard}/{$version}/{$modelName}{$lifeCycle}.php"),
                    contents: str_replace(['{{ $model }}', '{{ $guard }}', '{{ $version }}', '{{ $var }}', '{{ $lifeCycle }}'], [$modelName, $guard, $version, $var, $lifeCycle], $this->stub(key: $stubKey))
                );
                if (! $result) {
                    $this->pushError(message: "Failed to {$this->errorType} event file {$guard} {$version}");
                }
            }
        }

        return $this;
    }

    public function request(): self
    {
        $this->ensurePackageMade();

        $lifeCycles = [
            RequestType::API->value => [
                'Index',
                'Store',
                'Show',
                'Update',
                'Destroy',
                'Trashed',
                'Restore',
                'ForceDestroy'
            ],
            RequestType::WEB->value => [
                'Index',
                'Create',
                'Store',
                'Show',
                'Edit',
                'Update',
                'Destroy',
                'Trashed',
                'Restore',
                'ForceDestroy'
            ],
            RequestType::ADMIN->value => [
                'Index',
                'Store',
                'Show',
                'Update',
                'Destroy',
                'Trashed',
                'Restore',
                'ForceDestroy'
            ],
        ];

        $version = str($this->version)->ucfirst()->toString();
        $modelName = $this->packageName;

        foreach ($this->guards as $guard) {
            $requestLifeCycles = $lifeCycles[$guard];
            $guard = str($guard)->ucfirst()->toString();
            $result = $this->mkdir(
                path: $this->packageDir(append: "src/App/Http/Requests/{$guard}/{$version}"),
                recursive: true,
            );
            if (! $result) {
                $this->pushError(message: "Failed to {$this->errorType} requests folder {$guard} {$version}");
                return $this;
            }

            foreach ($requestLifeCycles as $lifeCycle) {
                $result = $this->mkfile(path: $this->packageDir(
                    append: "src/App/Http/Requests/{$guard}/{$version}/{$modelName}{$lifeCycle}Request.php"),
                    contents: str_replace(['{{ $model }}', '{{ $guard }}', '{{ $version }}', '{{ $lifeCycle }}'], [$modelName, $guard, $version, $lifeCycle], $this->stub(key: 'request'))
                );
                if (! $result) {
                    $this->pushError(message: "Failed to {$this->errorType} request file {$guard} {$version}");
                }
            }
        }

        return $this;
    }

    public function controller(): self
    {
        $this->ensurePackageMade();

        $version = str($this->version)->ucfirst()->toString();
        $modelName = $this->packageName;
        $singularVar = str($modelName)->lower()->singular();

        foreach ($this->guards as $guard) {
            $lowerGuard = $guard;
            $guard = str($guard)->ucfirst()->toString();
            $result = $this->mkdir(
                path: $this->packageDir(append: "src/App/Http/Controllers/{$guard}/{$version}"),
                recursive: true,
            );
            if (! $result) {
                $this->pushError(message: "Failed to {$this->errorType} controllers folder {$guard} {$version}");
                return $this;
            }

            $result = $this->mkfile(path: $this->packageDir(
                append: "src/App/Http/Controllers/{$guard}/{$version}/{$modelName}Controller.php"),
                contents: str_replace(['{{ $model }}', '{{ $guard }}', '{{ $version }}', '{{ $var }}'], [$modelName, $guard, $version, $singularVar], $this->stub(key: "controller.{$lowerGuard}"))
            );
            if (! $result) {
                $this->pushError(message: "Failed to {$this->errorType} controller file {$guard} {$version}");
            }
        }

        return $this;
    }

    public function route(): self
    {
        $this->ensurePackageMade();

        $modelName = $this->packageName;
        $routeKeyName = str($modelName)->singular()->snake('_')->lower()->toString();
        $controller = str($modelName)->singular()->snake()->lower()->toString();

        foreach ($this->guards as $guard) {
            $result = $this->mkdir(path: $this->packageDir(append: "routes/{$guard}"), recursive: true);
            if (! $result) {
                $this->pushError(message: "Failed to {$this->errorType} route folder {$this->packageName} {$this->version}");
            }

            $result = $this->mkfile(path: $this->packageDir(
                append: "routes/{$guard}/{$this->version}.php"),
                contents: str_replace(['{{ $model }}', '{{ $routeKeyName }}', '{{ $controller }}'], [$modelName, $routeKeyName, $controller], $this->stub(key: "route.{$guard}"))
            );
            if (! $result) {
                $this->pushError(message: "Failed to {$this->errorType} route file {$this->packageName}");
            }
        }


        return $this;
    }

    public function lang(): self
    {
        $this->ensurePackageMade();

        $locales = \Base::config(key: 'locales');

        foreach ($locales as $locale) {
            $result = $this->mkdir(path: $this->packageDir(append: "lang/$locale"), recursive: true);
            if (! $result) {
                $this->pushError(message: "Failed to {$this->errorType} lang folder {$this->packageName} {$this->version} {$locale}");
            }

            foreach ($this->guards as $guard) {
                $result = $this->mkfile(path: $this->packageDir(
                    append: "lang/{$locale}/{$guard}_{$this->version}.php"),
                    contents: str_replace([], [], $this->stub(key: "lang"))
                );
                if (! $result) {
                    $this->pushError(message: "Failed to {$this->errorType} lang file {$this->packageName} {$this->version} {$guard} {$locale}");
                }
            }
        }

        return $this;
    }

    public function migration(): self
    {
        $this->ensurePackageMade();

        $result = $this->mkdir(path: $this->packageDir(append: "database/migrations"), recursive: true);
        if (! $result) {
            $this->pushError(message: "Failed to {$this->errorType} database/migrations folder {$this->packageName}");
        }

        $tableName = str($this->packageName)->snake()->plural()->lower()->toString();

        foreach ($this->files->files($this->packageDir(append: "database/migrations")) as $file) {
            if (str($file->getFilename())->contains($tableName)) {
                return $this;
            }
        }

        $migrationName = now()->format('Y_m_d') . '_' . now()->timestamp . '_create_' . $tableName . '_table';
        $result = $this->mkfile(path: $this->packageDir(
            append: "database/migrations/$migrationName.php"),
            contents: str_replace(['{{ $table }}'], [$tableName], $this->stub(key: "migration"))
        );
        if (! $result) {
            $this->pushError(message: "Failed to {$this->errorType} migration file {$this->packageName} {$migrationName}");
        }

        return $this;
    }

    public function enum(): self
    {
        $this->ensurePackageMade();

        $result = $this->mkdir(path: $this->packageDir(append: "src/App/Enums"), recursive: true);
        if (! $result) {
            $this->pushError(message: "Failed to {$this->errorType} enums folder {$this->packageName}");
        }

        $enumsStub = [
            'status',
            'type',
        ];

        $modelName = $this->packageName;
        foreach ($enumsStub as $enumStub) {
            $enumName = $modelName . str($enumStub)->headline()->toString();
            $result = $this->mkfile(path: $this->packageDir(
                append: "src/App/Enums/$enumName.php"),
                contents: str_replace(['{{ $model }}'], [$modelName], $this->stub(key: "enum.{$enumStub}"))
            );
            if (! $result) {
                $this->pushError(message: "Failed to {$this->errorType} enum file {$this->packageName} {$enumName}");
            }
        }

        $locales = \Base::config(key: 'locales');

        foreach ($locales as $locale) {
            $result = $this->mkdir(path: $this->packageDir(append: "lang/$locale"), recursive: true);
            if (! $result) {
                $this->pushError(message: "Failed to {$this->errorType} lang folder {$this->packageName} {$this->version} {$locale}");
            }

            foreach ($this->guards as $guard) {
                $result = $this->mkfile(path: $this->packageDir(
                    append: "lang/{$locale}/enums.php"),
                    contents: str_replace([], [], $this->stub(key: "lang.enum"))
                );
                if (! $result) {
                    $this->pushError(message: "Failed to {$this->errorType} lang file {$this->packageName} {$this->version} {$guard} {$locale}");
                }
            }
        }

        return $this;
    }

    public function import(): self
    {
        $this->ensurePackageMade();

        $moduleName = $this->packageName;
        $version = str($this->version)->ucfirst()->toString();
        foreach ($this->guards as $guard) {
            $guard = str($guard)->ucfirst()->toString();

            $importPath = "src/App/Imports/$guard/$version";
            $result = $this->mkdir(path: $this->packageDir(append: $importPath), recursive: true);
            if (! $result) {
                $this->pushError(message: "Failed to {$this->errorType} import folder {$moduleName}");
            }

            $importName = str($moduleName)->headline()->plural()->toString();
            $repo = str($moduleName)->lower()->singular()->toString();
            $result = $this->mkfile(path: $this->packageDir(
                append: "$importPath/$importName.php",
            ), contents: str_replace(['{{ $model }}','{{ $guard }}','{{ $version }}','{{ $importName }}','{{ $repo }}'], [$moduleName,$guard,$version,$importName,$repo], $this->stub(key: "import.excel")));
            if (! $result) {
                $this->pushError(message: "Failed to {$this->errorType} import file {$moduleName} {$this->version} {$guard}");
            }
        }

        return $this;
    }

    public function export(): self
    {
        $this->ensurePackageMade();

        $moduleName = $this->packageName;
        $version = str($this->version)->ucfirst()->toString();
        foreach ($this->guards as $guard) {
            $guard = str($guard)->ucfirst()->toString();

            $exportPath = "src/App/Exports/$guard/$version";
            $result = $this->mkdir(path: $this->packageDir(append: $exportPath), recursive: true);
            if (! $result) {
                $this->pushError(message: "Failed to {$this->errorType} export folder {$moduleName}");
            }

            $exportName = str($moduleName)->headline()->plural()->toString();
            $repo = str($moduleName)->lower()->singular()->toString();
            $result = $this->mkfile(path: $this->packageDir(
                append: "$exportPath/$exportName.php",
            ), contents: str_replace(['{{ $model }}','{{ $guard }}','{{ $version }}','{{ $exportName }}','{{ $repo }}'], [$moduleName,$guard,$version,$exportName,$repo], $this->stub(key: "export.excel")));
            if (! $result) {
                $this->pushError(message: "Failed to {$this->errorType} export file {$moduleName} {$this->version} {$guard}");
            }
        }

        return $this;
    }

    public function serviceProvider(): self
    {
        $this->ensurePackageMade();

        $modelName = $this->packageName;
        $serviceKey = str($modelName)->snake('-')->lower()->toString();

        $result = $this->mkdir(path: $this->packageDir(append: 'src'));
        if (! $result) {
            $this->pushError(message: "Failed to {$this->errorType} src folder {$this->packageName}");
            return $this;
        }

        $result = $this->mkfile(path: $this->packageDir(
            append: "src/Callmeaf{$modelName}ServiceProvider.php"),
            contents: str_replace(['{{ $model }}', '{{ $serviceKey }}'], [$modelName, $serviceKey], $this->stub(key: "service_provider"))
        );
        if (! $result) {
            $this->pushError(message: "Failed to {$this->errorType} service provider file {$this->packageName}");
        }

        return $this;
    }

    public function composer(): self
    {
        $this->ensurePackageMade();

        $modelName = $this->packageName;
        $package = str($modelName)->snake('-')->lower()->toString();
        $desc = str($modelName)->headline()->plural()->toString();

        $result = $this->mkfile(path: $this->packageDir(
            append: "composer.json"),
            contents: str_replace(['{{ $model }}', '{{ $package }}', '{{ $desc }}'], [$modelName, $package, $desc], $this->stub(key: "composer"))
        );
        if (! $result) {
            $this->pushError(message: "Failed to {$this->errorType} composer.json {$this->packageName}");
        }

        return $this;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    private function ensurePackageMade(): void
    {
        if (! $this->remove) {
            if (! $this->packageMade) {
                throw new Error("Package does not exists, for creating new package please ensure you first called makePackage method at top level any methods of CallmeafPackageService.");
            }
        }
    }

    private function pushError(string $message): self
    {
        $caller = debug_backtrace(options: DEBUG_BACKTRACE_IGNORE_ARGS, limit: 2)[1]['function'];
        $this->errors[$caller] = $message;

        return $this;
    }

    private function stub(string $key): string
    {
        return $this->files->get(packagePath('base', "stubs/$key.stub"));
    }

    private function mkdir(string $path, bool $recursive = false, bool $force = false): bool
    {
        if ($this->checkFileExists(path: $path)) {
            if ($this->remove) {
                if (str($path)->endsWith(strtoupper($this->version))) {
                    return $this->files->deleteDirectory(directory: $path);
                }
            }
            return true;
        }
        if ($this->remove) {
            $this->pushError("Directory Not found. ( $path )");
            return false;
        }
        return $this->files->makeDirectory(path: $path, recursive: $recursive, force: $force);
    }

    private function mkfile(string $path, string $contents): int
    {
        if ($this->checkFileExists(path: $path)) {
            if ($this->remove) {
                return $this->files->delete(paths: $path);
            }
            return true;
        }
        if ($this->remove) {
            return true;
        }
        return $this->files->put(path: $path, contents: $contents);
    }

    private function checkFileExists(string $path): bool
    {
        return $this->files->exists(path: $path);
    }
}
