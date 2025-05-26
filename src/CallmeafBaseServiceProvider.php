<?php

namespace Callmeaf\Base;

use Callmeaf\Base\App\Console\Commands\CallmeafBasePackageCommand;
use Callmeaf\Base\App\Console\Commands\CallmeafPackageCommand;
use Callmeaf\Base\App\Console\Commands\CallmeafRemovePackageCommand;
use Callmeaf\Base\App\Http\Middleware\AdminMiddleware;
use Callmeaf\Base\App\Http\Middleware\BaseThrottleRequests;
use Callmeaf\Base\Contracts\ServiceProvider\HasCommand;
use Callmeaf\Base\Contracts\ServiceProvider\HasConfig;
use Callmeaf\Base\Contracts\ServiceProvider\HasFacade;
use Callmeaf\Base\Contracts\ServiceProvider\HasHelpers;
use Callmeaf\Base\Contracts\ServiceProvider\HasLang;
use Callmeaf\Base\Contracts\ServiceProvider\HasMiddleware;
use Callmeaf\Base\Contracts\ServiceProvider\HasRoute;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Routing\Router;

class CallmeafBaseServiceProvider extends CallmeafServiceProvider implements HasConfig, HasFacade, HasMiddleware, HasLang, HasCommand, HasHelpers, HasRoute
{
    public function serviceKey(): string
    {
        return 'base';
    }

    public function boot(): void
    {
        parent::boot();

        Relation::enforceMorphMap(\Base::relationMorphMap());
    }

    public function middlewares(Router $router): void
    {
        $router->aliasMiddleware('custom_throttle', BaseThrottleRequests::class);
        $router->aliasMiddleware('admin', AdminMiddleware::class);
    }

    public function commandsClass(): array
    {
        return [
            CallmeafPackageCommand::class,
            CallmeafBasePackageCommand::class,
            CallmeafRemovePackageCommand::class,
        ];
    }

    public function helpers(): array
    {
        return [
            '/helpers.php'
        ];
    }

    private function relationMorphMap(): ?array
    {
        $relationMorphMapClass = $this->serviceConfig('relation_morph_map') ?? null;

        if(is_null($relationMorphMapClass)) {
            return null;
        }

        return (new $relationMorphMapClass)();
    }
}
