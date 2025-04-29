<?php

namespace Callmeaf\Base\App\Traits\Package;

use Illuminate\Support\Str;

trait BasePackageMethods
{
    public function basePackageDir(string $append = ''): string
    {
        return packagePath('base', $append);
    }

    public function basePackageVersionExists(): bool
    {
        return $this->checkFileExists(path: $this->basePackageDir(
            append: "config/callmeaf-base-{$this->version}.php"
        ));
    }

    public function repairOrNewVersionOfBasePackage(): self
    {
        $this->baseConfig();
        $this->coreRepo();
        $this->baseRepo();
        $this->baseController();
        $this->baseRoute();

        return $this;
    }

    public function removeBasePackage(): self
    {
        if (! $this->remove) {
            $this->remove = true;
        }
        if (! $this->errorType) {
            $this->errorType = 'remove';
        }

        $this->baseConfig();
        $this->coreRepo();
        $this->baseRepo();
        $this->baseController();
        $this->baseRoute();

        return $this;
    }

    private function baseConfig(): void
    {
        $restrictRouteMiddlewareKey = Str::uuid();
        $result = $this->mkfile(path: $this->basePackageDir(
            append: "config/callmeaf-base-{$this->version}.php"),
            contents: str_replace(['{{ $version }}','{{ $restrictRouteMiddlewareKey }}'], [$this->version,$restrictRouteMiddlewareKey], $this->stub(key: 'config.base'))
        );
        if (! $result) {
            $this->pushError(message: "Failed to {$this->errorType} base config file {$this->version}");
        }
    }

    private function coreRepo(): void
    {
        $version = str($this->version)->ucfirst()->toString();
        $result = $this->mkdir(
            path: $this->basePackageDir(append: "src/App/Repo/{$version}"),
            recursive: true,
        );
        if (! $result) {
            $this->pushError(message: "Failed to {$this->errorType} core repo folder {$version}");
            return;
        }
        $result = $this->mkfile(path: $this->basePackageDir(
            append: "src/App/Repo/{$version}/CoreRepo.php"),
            contents: str_replace(['{{ $version }}'], [$version], $this->stub(key: 'repo.core'))
        );
        if (! $result) {
            $this->pushError(message: "Failed to {$this->errorType} core repo file {$version}");
        }
    }

    private function baseRepo(): void
    {
        $version = str($this->version)->ucfirst()->toString();

        $result = $this->mkfile(path: $this->basePackageDir(
            append: "src/App/Repo/{$version}/BaseRepo.php"),
            contents: str_replace(['{{ $version }}'], [$version], $this->stub(key: 'repo.base'))
        );
        if (! $result) {
            $this->pushError(message: "Failed to {$this->errorType} base repo file {$version}");
        }
    }

    private function baseController(): void
    {
        $version = str($this->version)->ucfirst()->toString();

        foreach ($this->guards as $guard) {
            $guard = str($guard)->ucfirst()->toString();
            $result = $this->mkdir(
                path: $this->basePackageDir(append: "src/App/Http/Controllers/{$guard}/{$version}"),
                recursive: true,
            );
            if (! $result) {
                $this->pushError(message: "Failed to {$this->errorType} base controllers folder {$guard} {$version}");
                return;
            }

            $result = $this->mkfile(path: $this->basePackageDir(
                append: "src/App/Http/Controllers/{$guard}/{$version}/{$guard}Controller.php"),
                contents: str_replace(['{{ $guard }}', '{{ $version }}'], [$guard, $version], $this->stub(key: "controller.base"))
            );
            if (! $result) {
                $this->pushError(message: "Failed to {$this->errorType} base controller file {$guard} {$version}");
            }
        }

    }

    private function baseRoute(): void
    {
        foreach ($this->guards as $guard) {
            $result = $this->mkdir(path: $this->basePackageDir(append: "routes/{$guard}"), recursive: true);
            if (! $result) {
                $this->pushError(message: "Failed to {$this->errorType} route folder {$this->packageName} {$this->version}");
            }

            $result = $this->mkfile(path: $this->basePackageDir(
                append: "routes/{$guard}/{$this->version}.php"),
                contents: str_replace([], [], $this->stub(key: "route.{$guard}.base"))
            );
            if (! $result) {
                $this->pushError(message: "Failed to {$this->errorType} route file {$this->packageName}");
            }
        }
    }
}
