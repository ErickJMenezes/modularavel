<?php

namespace ErickJMenezes\Modularavel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Composer;

class InstallCommand extends Command
{
    protected $signature = 'module:install';

    protected $description = 'Install the package in your project';

    public function handle(Composer $composer): void
    {
        $composer->setWorkingPath(base_path())
            ->modify(function (array $composerFile) {
                $composerFile['extra']['merge-plugin'] = [
                    "include" => [
                        'libs/*/composer.json'
                    ],
                    "require" => [],
                    "recurse" => true,
                    "replace" => false,
                    "ignore-duplicates" => true,
                    "merge-dev" => true,
                    "merge-extra" => true,
                    "merge-extra-deep" => true,
                    "merge-replace" => true,
                    "merge-scripts" => false
                ];

                return $composerFile;
            });

        $this->info('Done!');
    }
}
