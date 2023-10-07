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
use Illuminate\Support\Composer;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class MakeLib extends Command
{
    protected $signature = 'make:lib
                            {name : The name of the new library. Example: "shopping-cart"}';

    protected $description = 'Generates a new library';

    public function handle(Generator $generator, Composer $composer)
    {
        $baseDirectory = (string) config('modularavel.libraries_directory');
        $libName = (string) $this->argument('name');
        $laravelVersion = InstalledVersions::getPrettyVersion('laravel/framework');

        $this->info("Scaffolding \"$libName\"...");

        $this->scaffoldLibrary($generator, $libName, $laravelVersion, $baseDirectory);

        $this->info('Generated files and folders.');

        $this->info('Bootstrapping library...');

        $this->bootstrappingLibrary($composer, $baseDirectory, $libName);

        $this->info('Registering the new library...');

        $this->registerLibrary($composer, $libName);

        $this->info("Done.");
        $this->newLine();
    }

    private function scaffoldLibrary(
        Generator $generator,
        string $libName,
        ?string $laravelVersion,
        string $baseDirectory
    ): void
    {
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
                        '--no-interaction',
                    ]),
                    new File('.gitignore', "/vendor\n/node_modules\n.phpunit.result.cache\n"),
                ])),
            ]),
            $baseDirectory,
        );
    }

    private function bootstrappingLibrary(
        Composer $composer,
        string $baseDirectory,
        string $libName,
    ): void
    {
        $libDir = $baseDirectory.DIRECTORY_SEPARATOR.$libName;
        $composer->setWorkingPath($libDir)
            ->modify(function (array $file) use ($libName) {
                $lib = Str::studly($libName);
                $file['extra']['laravel']['providers'][] = "Libs\\{$lib}\\Providers\\{$lib}ServiceProvider";
                $file['config']['allow-plugins']['pestphp/pest-plugin'] = true;
                return $file;
            });
        $composer->requirePackages(["orchestra/testbench", "pestphp/pest"], true, $this->getOutput());

        (new Process(
            [$libDir.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'bin'.DIRECTORY_SEPARATOR.'pest', '--init', '--no-interaction'],
            $libDir,
        ))->enableOutput()->run();

        (new Process(
            [$libDir.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'bin'.DIRECTORY_SEPARATOR.'testbench', 'workbench:install'],
            $libDir,
        ))->enableOutput()->run();
    }

    private function registerLibrary(Composer $composer, string $libName): void
    {
        $composer->setWorkingPath(base_path())
            ->modify(function (array $file) use ($libName) {
                $file['repositories'][] = [
                    'type' => 'path',
                    'url' => "libs/$libName",
                    'symlink' => true,
                ];
                return $file;
            });
        $composer->requirePackages(["libs/$libName:@dev"], output: $this->getOutput());
    }
}
