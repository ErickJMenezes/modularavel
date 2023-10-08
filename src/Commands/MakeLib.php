<?php

namespace ErickJMenezes\Modularavel\Commands;

use Composer\InstalledVersions;
use ErickJMenezes\Modularavel\Scaffolding\Command as GeneratorCommand;
use ErickJMenezes\Modularavel\Scaffolding\File;
use ErickJMenezes\Modularavel\Scaffolding\Folder;
use ErickJMenezes\Modularavel\Scaffolding\Generator;
use ErickJMenezes\Modularavel\Scaffolding\Stubs\PestMainFile;
use ErickJMenezes\Modularavel\Scaffolding\Stubs\ServiceProviderFile;
use ErickJMenezes\Modularavel\Scaffolding\Stubs\TestCaseFile;
use ErickJMenezes\Modularavel\Scaffolding\Tree;
use Illuminate\Console\Command;
use Illuminate\Support\Composer;
use Illuminate\Support\Str;

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
                        new File("web.php", "<?php\n\nuse Illuminate\Support\Facades\Route;\n")
                    ])),
                    new Folder('config', new Tree([
                        new File("$libName.php", "<?php\n\nreturn [];")
                    ])),
                    new Folder('resources', new Tree([
                        new Folder('views', new Tree([
                            new File('.gitkeep'),
                        ])),
                        new Folder('lang', new Tree([
                            new File('.gitkeep'),
                        ])),
                    ])),
                    new File('.gitignore', "/vendor\n/node_modules\n.phpunit.result.cache\n"),
                    //
                    new GeneratorCommand([
                        'composer',
                        'init',
                        "--name=libs/$libName",
                        "--stability=stable",
                        "--autoload=src",
                        '--no-interaction',
                    ]),
                    new GeneratorCommand([
                        'composer',
                        'require',
                        "laravel/framework:^$laravelVersion",
                    ]),
                    new GeneratorCommand([
                        'composer',
                        'require',
                        'orchestra/testbench',
                        'pestphp/pest',
                        'pestphp/pest-plugin-laravel',
                        '--dev',
                    ]),
                    new GeneratorCommand([
                        'vendor'.DIRECTORY_SEPARATOR.'bin'.DIRECTORY_SEPARATOR.'testbench',
                        'workbench:install',
                        '--no-interaction',
                    ]),
                    new GeneratorCommand([
                        'vendor'.DIRECTORY_SEPARATOR.'bin'.DIRECTORY_SEPARATOR.'pest',
                        '--init',
                        '--no-interaction',
                    ]),
                    new GeneratorCommand([
                        'rm',
                        '-rf',
                        'workbench',
                        'testbench.yaml',
                        'tests/Pest.php',
                        'tests/TestCase.php',
                    ]),
                    new File('testbench.yaml', 'laravel: ../../.'),
                    new Folder('tests', new Tree([
                        new TestCaseFile($libName),
                        new PestMainFile($libName),
                    ]))
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
                $file['type'] = 'library';
                $file['description'] = "internal library";
                $file['license'] = 'MIT';
                $file['autoload-dev'] = [
                    'files' => [
                        '../../vendor/autoload.php',
                        'tests/TestCase.php',
                    ]
                ];
                return $file;
            });
        $composer->dumpAutoloads();
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
