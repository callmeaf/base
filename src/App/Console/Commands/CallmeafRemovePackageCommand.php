<?php

namespace Callmeaf\Base\App\Console\Commands;

use Callmeaf\Base\App\Enums\RequestType;
use Callmeaf\Base\App\Services\CallmeafPackageService;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class CallmeafRemovePackageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'callmeaf:remove-package {package : which package name should be removed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove callmeaf package files in specific versions and guards.';


    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $package = $this->argument(key: 'package');
        $isBasePackage = str($package)->lower()->toString() === 'base';

        $userSelectedVersion = $this->ask("Which version do you prefer to remove for {$package} package", 'v1');

        if (! $this->isValidVersion(version: $userSelectedVersion)) {
            $this->error('Package version must follow this example: v1,v2,v3,....');
            return 1;
        }


        $guards = array_map(fn($item) => $item->value, RequestType::cases());
        $packageService = new CallmeafPackageService(packageName: $package, version: $userSelectedVersion, guards: $guards);

        if($isBasePackage) {
            if(! $packageService->basePackageVersionExists()) {
                $this->error("Base with $userSelectedVersion does not exists.");
                return 1;
            }
        } else {
            if(! $packageService->versionExists()) {
                $this->error("$package with $userSelectedVersion does not exists");
                return 1;
            }
        }


        if ($packageService->basePackageVersionExists() && ! $isBasePackage) {
            if ($this->confirm("Do you want remove this version of base package also ? Base ( {$userSelectedVersion} )")) {
                $packageService->removeBasePackage();
            }
        }

        if ($isBasePackage) {
            $packageService->removeBasePackage();
        } else {
            $this->alert("Removing the $package! ( $userSelectedVersion ) it may take a while, please wait.");
            $packageService->removePackage();
        }

        $errors = $packageService->getErrors();
        foreach ($errors as $error) {
            $this->error($error);
        }
        if (! empty($errors)) {
            return 1;
        }

        $this->info("Package successful removed. ( $package ) ( $userSelectedVersion )");
        return 0;
    }

    private function isValidVersion(string $version): bool
    {
        $version = str($version);
        return $version->startsWith('v') && preg_match('/^[1-9]\d*$/', $version->after('v')->toString()) && ! $version->contains('.');
    }

}
