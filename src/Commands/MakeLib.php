<?php

namespace ErickJMenezes\Modularavel\Commands;

use ErickJMenezes\Modularavel\UseCases\GenerateLibrary;
use Illuminate\Console\Command;

class MakeLib extends Command
{
    protected $signature = 'make:lib
                            {name : The name of the new library. Example: "shopping-cart".}
                            {--with-routes : Creates the lib with pre-configured routes file and controllers folder structure.}
                            {--with-views : Creates the lib with pre-configured views folder.}
                            {--with-commands : Creates the lib with pre-created commands folder.}
                            {--with-config-file : The library will publish a config file.}
                            {--with-migrations : Creates the lib with pre-configured migrations folder.}
                            {--minimal : Creates the lib without the default packages (pest, laravel/framework, and pre-configured testbench). The configuration is by yourself.}';

    protected $description = 'Generates a new library';

    public function handle(GenerateLibrary $generateLibrary)
    {
        $libName = (string) $this->argument('name');

        $this->info("Started the scaffolding of \"$libName\".");

        $generateLibrary->generate(
            $libName,
            $this->option('with-commands'),
            $this->option('with-routes'),
            $this->option('with-views'),
            $this->option('with-config-file'),
            $this->option('with-migrations'),
            $this->option('minimal'),
            fn($type, $line) => $this->getOutput()->write('    '.$line),
        );

        $this->info("Done.");
        $this->newLine();
    }
}
