<?php

namespace Callmeaf\Base;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class CallmeafBaseServiceProvider extends ServiceProvider
{
    public function boot()
    {
        require __DIR__ . '/helpers.php';
        $this->registerConfig();
        $this->registerLang();
    }

    private function registerConfig(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/callmeaf-base.php','callmeaf-base');
        $this->publishes([
            __DIR__ . '/../config/callmeaf-base.php' => config_path('callmeaf-base.php'),
        ],'callmeaf-base-config');
    }

    private function registerLang(): void
    {
        $langPathFromResource = lang_path('vendor/callmeaf');
        if(is_dir($langPathFromResource)) {
            $this->loadTranslationsFrom($langPathFromResource,'callmeaf');
        } else {
            $this->loadTranslationsFrom(__DIR__ . '/../lang','callmeaf');
        }
        $this->publishes([
            __DIR__ . '/../lang/base-v1.php' => lang_path('vendor/callmeaf/base-v1.php'),
        ],'callmeaf-base-lang');
    }
}
