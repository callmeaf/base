<?php

namespace Callmeaf\Base;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class CallmeafBaseServiceProvider extends ServiceProvider
{
    private const CONFIGS_DIR = __DIR__ . '/../config';
    private const LANG_DIR = __DIR__ . '/../lang';
    private const LANG_NAMESPACE = 'callmeaf_base';

    public function boot()
    {
        require_once( __DIR__ . '/helpers.php');
        $this->registerConfig();
        $this->registerLang();
    }

    private function registerConfig(): void
    {
        $this->mergeConfigFrom(self::CONFIGS_DIR . '/callmeaf-base.php','callmeaf-base');
        $this->publishes([
            self::CONFIGS_DIR . '/callmeaf-base.php' => config_path('callmeaf-base.php'),
        ],'callmeaf-base-config');
    }

    private function registerLang(): void
    {
        $langPathFromVendor = lang_path('vendor/callmeaf/base');
        if(is_dir($langPathFromVendor)) {
            $this->loadTranslationsFrom($langPathFromVendor,self::LANG_NAMESPACE);
        } else {
            $this->loadTranslationsFrom(self::LANG_DIR,self::LANG_NAMESPACE);
        }
        $this->publishes([
            self::LANG_DIR => $langPathFromVendor,
        ],'callmeaf-base-lang');
    }
}
