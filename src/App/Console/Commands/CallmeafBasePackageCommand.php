<?php

namespace Callmeaf\Base\App\Console\Commands;

use Callmeaf\Base\App\Services\CallmeafPackageService;
use Illuminate\Console\Command;

class CallmeafBasePackageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'callmeaf:base-package {--repair}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage base package files';


    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $errors = collect();
        $messages = collect();
        if ($this->option('repair')) {
            [$message, $repairErrors] = $this->repairPackage();

            $messages->push($message);
            $errors->push($repairErrors);
        }


        $errors = $errors->collapse();

        if ($errors->isNotEmpty()) {
            foreach ($errors as $error) {
                $this->error($error);
            }
            return 1;
        }

        foreach ($messages as $message) {
            $this->info($message);
        }
        return 0;
    }


    private function repairPackage(): array
    {
        $errors = [];
        foreach (allExistsVersion() as $version) {
            $packageService = new CallmeafPackageService(packageName: 'base', version: $version, guards: ['api', 'web', 'admin']);
            $packageService->repairOrNewVersionOfBasePackage();
            foreach ($packageService->getErrors() as $error) {
                $errors[] = $error;
            }
        }

        return ['Successful repair base package.', $errors];
    }

}
