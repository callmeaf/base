<?php

namespace Callmeaf\Base;

use Illuminate\Support\ServiceProvider;

class AfBaseServiceProvider extends ServiceProvider
{
    public function boot()
    {
        require __DIR__ . '/helpers.php';
        $this->registerConfig();
        $this->registerLang();
    }

    private function registerConfig(): void
    {
        $this->mergeConfigFrom(__DIR__.'/config/callmeaf-base.php','callmeaf-base');
        $this->publishes([
            __DIR__.'/config/callmeaf-base.php' => config_path('callmeaf-base.php'),
        ],'callmeaf-base-config');
    }

    private function registerLang(): void
    {
        $langPathFromResource = lang_path('callmeaf');
        if(is_dir($langPathFromResource)) {
            $this->loadTranslationsFrom($langPathFromResource,'callmeaf::base.v1');
        } else {
            $this->loadTranslationsFrom(__DIR__.'/lang/callmeaf-base-v1.php','callmeaf::base.v1');
        }
        $this->publishes([
            __DIR__.'/lang/callmeaf-base-v1.php' => lang_path('callmeaf/base-v1.php'),
        ],'callmeaf-base-lang');
    }
}
