<?php

namespace Callmeaf\Base;

use Callmeaf\Base\App\Enums\RequestType;
use Callmeaf\Base\Contracts\ServiceProvider\HasCommand;
use Callmeaf\Base\Contracts\ServiceProvider\HasConfig;
use Callmeaf\Base\Contracts\ServiceProvider\HasEvent;
use Callmeaf\Base\Contracts\ServiceProvider\HasFacade;
use Callmeaf\Base\Contracts\ServiceProvider\HasHelpers;
use Callmeaf\Base\Contracts\ServiceProvider\HasLang;
use Callmeaf\Base\Contracts\ServiceProvider\HasMiddleware;
use Callmeaf\Base\Contracts\ServiceProvider\HasMigration;
use Callmeaf\Base\Contracts\ServiceProvider\HasRepo;
use Callmeaf\Base\Contracts\ServiceProvider\HasRoute;
use Callmeaf\Base\Contracts\ServiceProvider\HasSeeder;
use Callmeaf\Base\Contracts\ServiceProvider\HasView;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

abstract class CallmeafServiceProvider extends ServiceProvider
{
    const PREFIX_KEY = "callmeaf-";
    private bool $CONFIG_STATUS = false;

    abstract protected function serviceKey(): string;

    public function serviceConfig(?string $key = null): mixed
    {
        $config = config(self::PREFIX_KEY . $this->serviceKey() . '-' . requestVersion());
        return $key ? $config[$key] : $config;
    }

    public function register(): void
    {
        $this->registerHelpers();
        $this->registerConfig();
        if ($this->CONFIG_STATUS) {
            $this->registerFacade();
            $this->registerRepo();
            $this->registerEvent();
            $this->registerLang();
            $this->registerCommand();
        }
    }

    public function boot(): void
    {
        if ($this->CONFIG_STATUS) {
            $this->registerRoute();
            $this->registerMigration();
            $this->registerMiddleware();
            $this->registerView();
            $this->registerSeeder();
        }
    }

    private function dir(string $path = ''): string
    {
        $refClass = new \ReflectionClass($this);

        return dirname($refClass->getFileName()) . $path;
    }

    private function registerHelpers(): void
    {
        if ($this instanceof HasHelpers) {
            foreach ($this->helpers() as $helper) {
                require_once($this->dir(path: $helper));
            }
        }
    }

    private function registerConfig(): void
    {
        if($this instanceof HasConfig) {
            $configDir = $this->dir(path: '/../config');

            $version = requestVersion();
            $serviceKey = $this->serviceKey();
            $configKey = self::PREFIX_KEY . $serviceKey . '-' . $version;
            $configGroup = "{$configKey}-config";
            $configFile = "{$configKey}.php";
            $configPath = "{$configDir}/{$configFile}";

            if (! app(Filesystem::class)->exists($configPath)) {
                $this->setConfigStatus(false);
                return;
            }

            $this->mergeConfigFrom($configPath,$configKey);
            $this->publishes([
                $configPath => config_path($configFile),
            ],$configGroup);

            $this->setConfigStatus(true);
        }
    }

    private function registerFacade(): void
    {
        if ($this instanceof HasFacade) {
            foreach ($this->serviceConfig(key: 'facades') as $facadeConfig) {
                $service = $facadeConfig['service'];
                $facade = $facadeConfig['facade'];
                $alias = $facadeConfig['alias'];

                $this->app->singleton(abstract: $service, concrete: function (\Illuminate\Contracts\Foundation\Application $app) use ($service) {
                    return new $service();
                });
                if(! class_exists($alias)) {
                    class_alias(class: $facade, alias: $alias);
                }
            }
        }
    }

    private function registerRepo(): void
    {
        if($this instanceof HasRepo) {
            $repo = $this->serviceConfig(key: 'repo');
            $this->app->bind($this->repo(), function (Application $app) use ($repo) {
                return new $repo($this->serviceConfig(key: 'model'));
            });
        }
    }

    private function registerRoute(): void
    {
        if($this instanceof HasRoute) {
            $version = requestVersion();
            $type = requestType();
            $routePath = $this->dir(path: "/../routes/{$type}/{$version}.php");

            match ($type) {
                RequestType::API->value => $this->loadApiRoutes(routePath: $routePath),
                RequestType::WEB->value => $this->loadWebRoutes(routePath: $routePath),
                RequestType::ADMIN->value => $this->loadAdminRoutes(routePath: $routePath),
                default => null,
            };
        }
    }

    private function loadApiRoutes(string $routePath): void
    {
        if (! app(Filesystem::class)->exists($routePath)) {
            return;
        }

        Route::prefix(\Base::apiPrefix())->middleware(\Base::apiMiddleware())->as(\Base::apiAs())->group(function () use ($routePath) {
            $this->loadRoutesFrom(path: $routePath);
        });
    }

    private function loadWebRoutes(string $routePath): void
    {
        if (! app(Filesystem::class)->exists($routePath)) {
            return;
        }
        Route::prefix(\Base::webPrefix())->middleware(\Base::webMiddleware())->as(\Base::webAs())->group(function () use ($routePath) {
            $this->loadRoutesFrom(path: $routePath);
        });
    }

    private function loadAdminRoutes(string $routePath): void
    {
        if (! app(Filesystem::class)->exists($routePath)) {
            return;
        }
        Route::prefix(\Base::adminPrefix())->middleware(\Base::adminMiddleware())->as(\Base::adminAs())->group(function () use ($routePath) {
            $this->loadRoutesFrom(path: $routePath);
        });
    }

    private function registerMigration(): void
    {
        if($this instanceof HasMigration) {
            $databaseDir = $this->dir(path: '/../database');
            $migrationPath = "{$databaseDir}/migrations";
            $databaseGroup = self::PREFIX_KEY . "{$this->serviceKey()}-migrations";

            $this->loadMigrationsFrom($migrationPath);
            $this->publishes([
                $migrationPath => database_path('migrations'),
            ],$databaseGroup);
        }
    }

    private function registerMiddleware(): void
    {
        if ($this instanceof HasMiddleware) {
            $this->middlewares(router: $this->app->make(Router::class));
        }

    }

    private function registerEvent(): void
    {
        if ($this instanceof HasEvent) {
            $allEvents = $this->serviceConfig(key: 'events');
            foreach ($allEvents as $events) {
                foreach ($events as $event => $listeners) {
                    foreach ($listeners as $listener) {
                        Event::listen($event,$listener);
                    }
                }
            }
        }
    }

    private function registerLang(): void
    {
        if ($this instanceof HasLang) {
            $serviceKey = $this->serviceKey();
            $langNameSpace = self::PREFIX_KEY . $serviceKey;
            $langDir = $this->dir(path: '/../lang');
            $vendorLangDir = $this->app->langPath(path: 'vendor/' . $serviceKey);
            $langGroup = self::PREFIX_KEY . "{$serviceKey}-lang";

            if (is_dir($vendorLangDir)) {
                $this->loadTranslationsFrom(path: $vendorLangDir, namespace: $langNameSpace);
            } else {
                $this->loadTranslationsFrom(path: $langDir, namespace: $langNameSpace);
            }

            $this->publishes([
                $langDir => $vendorLangDir,
            ], $langGroup);
        }
    }

    private function registerCommand(): void
    {
        if ($this instanceof HasCommand) {
            if ($this->app->runningInConsole()) {
                $this->commands(commands: $this->commandsClass());
            }
        }
    }

    private function registerView(): void
    {
        if ($this instanceof HasView) {
            $viewDir = $this->dir(path: '/../resources/views');
            $serviceKey = $this->serviceKey();
            $viewGroup = self::PREFIX_KEY . $serviceKey;
            $this->loadViewsFrom($viewDir, $viewGroup);

            $this->publishes([
                $viewDir => resource_path("views/vendor/$viewGroup")
            ]);
        }
    }

    private function registerSeeder(): void
    {
        if($this instanceof HasSeeder) {
            $seeders = $this->seeders();
            $this->app->afterResolving(DatabaseSeeder::class,function($databaseSeeder) use ($seeders) {
                foreach ($seeders as $seeder) {
                    $databaseSeeder->call($seeder);
                }
            });
        }
    }

    private function setConfigStatus(bool $value): void
    {
        $this->CONFIG_STATUS = $value;
    }
}
