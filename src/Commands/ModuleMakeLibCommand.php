<?php

namespace ErickJMenezes\Modularavel\Commands;

use Composer\InstalledVersions;
use ErickJMenezes\Modularavel\Scaffolding\ServiceProviderFile;
use ErickJMenezes\Modularavel\Scaffolding\File;
use ErickJMenezes\Modularavel\Scaffolding\Folder;
use ErickJMenezes\Modularavel\Scaffolding\Tree;
use ErickJMenezes\Modularavel\Scaffolding\Generator;
use Illuminate\Console\Command;
use ErickJMenezes\Modularavel\Scaffolding\Command as GeneratorCommand;

class ModuleMakeLibCommand extends Command
{
    protected $signature = 'module:make
                            {name : The name of the new library. Example: "shopping-cart"}';

    protected $description = 'Generates a new library';

    public function handle(Generator $generator)
    {
        $baseDirectory = (string) config('modularavel.libraries_directory');
        $libName = (string)$this->argument('name');
        $laravelVersion = InstalledVersions::getPrettyVersion('laravel/framework');

        $this->info("Generating scaffold of \"$libName\"...");

        $generator->generate(
            new Tree([
                new Folder($libName, new Tree([
                    new Folder('src', new Tree([
                        new Folder('Commands', new Tree([
                            new File('.gitkeep'),
                        ])),
                        new Folder('Http', new Tree([
                            new Folder('Controllers', new Tree([
                                new File('.gitkeep'),
                            ])),
                            new Folder('Requests', new Tree([
                                new File('.gitkeep'),
                            ])),
                            new Folder('Resources', new Tree([
                                new File('.gitkeep'),
                            ])),
                        ])),
                        new Folder('Models', new Tree([
                            new File('.gitkeep'),
                        ])),
                        new Folder('Providers', new Tree([
                            new ServiceProviderFile($libName)
                        ])),
                    ])),
                    new Folder('routes', new Tree([
                        new File("web.php", "<?php")
                    ])),
                    new Folder('config', new Tree([
                        new File("$libName.php", "<?php\n\nreturn [];")
                    ])),
                    new Folder('lang', new Tree([
                        new File('.gitkeep'),
                    ])),
                    new Folder('resources', new Tree([
                        new Folder('views', new Tree([
                            new File('.gitkeep'),
                        ])),
                    ])),
                    new GeneratorCommand([
                        'composer',
                        'init',
                        "--name=libs/$libName",
                        "--stability=stable",
                        "--autoload=src",
                        "--require=laravel/framework:^$laravelVersion",
                        "--require=php:^8.2",
                        '--require-dev=pestphp/pest:^2.21',
                        '--no-interaction',
                    ]),
                    new File('.gitignore', "/vendor\n/node_modules\n.phpunit.result.cache\n"),
                ]))
            ]),
            $baseDirectory,
        );

        $this->info("The generation is now completed!");
        $this->newLine();
        $this->line("Now you must add the ");
        $this->line("cd libs/$libName");
        $this->line("composer install");
        $this->line("./vendor/bin/pest init");
        $this->newLine();
    }
}
