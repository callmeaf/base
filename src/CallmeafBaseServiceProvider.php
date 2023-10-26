<?php

namespace Callmeaf\Base;

use Illuminate\Support\ServiceProvider;

class CallmeafBaseServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->mergeConfigFrom(__DIR__.'/config/callmeaf-base.php','callmeaf-base');
        $this->publishes([
            __DIR__.'/config/callmeaf-base.php' => config_path('callmeaf-base.php'),
        ],'callmeaf-base-config');
    }
}
