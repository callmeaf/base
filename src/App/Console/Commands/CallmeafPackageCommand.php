<?php

namespace Callmeaf\Base\App\Console\Commands;

use Callmeaf\Base\App\Enums\RequestType;
use Callmeaf\Base\App\Services\CallmeafPackageService;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class CallmeafPackageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'callmeaf:package {package : which package name should be created} {--pivot} {--trashed=true}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make callmeaf package with default files';


    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $package = $this->argument(key: 'package');
        $isPivot = (bool) $this->option('pivot');
        $hasTrashed = (bool) $this->option('trashed');
        if ($package === 'base') {
            $this->error('Can not use base name as package, try another name.');
            return 1;
        }

        $userSelectedVersion = $this->ask("Which version do you prefer to create for {$package} package", 'v1');

        if (! $this->isValidVersion(version: $userSelectedVersion)) {
            $this->error('Package version must follow this example: v1,v2,v3,....');
            return 1;
        }

        $options = $isPivot ? ['all','model','repo','resource','lang','migration','enum'] : ['all', 'model', 'repo', 'resource', 'event', 'request', 'controller', 'route', 'lang', 'migration', 'enum','import','export'];
        $userSelectedOptions = $this->choice("Which options you want to create for {$package} package", $options, default: 0, multiple: true);

        if (! empty(array_filter($userSelectedOptions, fn($item) => $item === 'all'))) {
            unset($options[0]);
        } else {
            $options = $userSelectedOptions;
        }
        $options = ['serviceProvider', 'composer', 'config', ...$options, 'autoDiscoverPackage'];

        $guards = ['all', ...array_map(fn($item) => $item->value, RequestType::cases())];
        $userSelectedGuards = $this->choice("Which guard you want use in your app for {$package} package", $guards, default: 0, multiple: true);

        if (! empty(array_filter($userSelectedGuards, fn($item) => $item === 'all'))) {
            unset($guards[0]);
        } else {
            $guards = $userSelectedGuards;
        }

        $packageService = new CallmeafPackageService(packageName: $package, version: $userSelectedVersion, guards: $guards,isPivot: $isPivot,hasTrashed: $hasTrashed);
        if (! $packageService->basePackageVersionExists()) {
            $this->warn("For new version {$package} {$userSelectedVersion} you must first create base config of this version");
            if ($this->confirm("create new version of base config ?", true)) {
                $packageService->repairOrNewVersionOfBasePackage();
            } else {
                $this->error("Can not create new version of {$package} {$userSelectedVersion} because base package version does not sync with that");
                return 1;
            };
        }

        if ($packageService->versionExists()) {
            $this->warn("Package already created for version: {$userSelectedVersion} in " . $packageService->packageDir());
            if (! $this->confirm("would you like create missing files ?", true)) {
                $this->error("Package already created for version: {$userSelectedVersion} in " . $packageService->packageDir());
                return 1;
            };
        }
        $packageService->makePackage();

        foreach ($options as $option) {
            if($option === 'autoDiscoverPackage') {
                $this->alert("Preparing the $package! ( $userSelectedVersion ) it may take a while, please wait.");
            }
            $packageService->{$option}();
        }

        $errors = $packageService->getErrors();
        foreach ($errors as $error) {
            $this->error($error);
        }
        if (! empty($errors)) {
            return 1;
        }



        $this->info("Package successful created. ( $package ) ( $userSelectedVersion )");
        return 0;
    }

    private function isValidVersion(string $version): bool
    {
        $version = str($version);
        return $version->startsWith('v') && preg_match('/^[1-9]\d*$/', $version->after('v')->toString()) && ! $version->contains('.');
    }

}
