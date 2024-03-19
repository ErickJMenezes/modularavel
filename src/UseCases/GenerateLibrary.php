<?php

namespace ErickJMenezes\Modularavel\UseCases;

use Closure;
use Composer\InstalledVersions;
use ErickJMenezes\Modularavel\Scaffolding\Command as GeneratorCommand;
use ErickJMenezes\Modularavel\Scaffolding\File;
use ErickJMenezes\Modularavel\Scaffolding\Folder;
use ErickJMenezes\Modularavel\Scaffolding\Generator;
use ErickJMenezes\Modularavel\Scaffolding\Stubs\PestMainFile;
use ErickJMenezes\Modularavel\Scaffolding\Stubs\ServiceProviderFile;
use ErickJMenezes\Modularavel\Scaffolding\Stubs\TestCaseFile;
use ErickJMenezes\Modularavel\Scaffolding\Tree;
use ErickJMenezes\Modularavel\Scaffolding\When;
use Illuminate\Support\Composer;
use Illuminate\Support\Str;

readonly class GenerateLibrary
{
    public function __construct(
        private Generator $generator,
        private Composer $composer,
        private string $baseDirectory,
        private string $basePath,
    )
    {
    }

    public function generate(
        string $libName,
        bool $withCommands,
        bool $withRoutes,
        bool $withViews,
        bool $withConfigFile,
        bool $withMigrations,
        bool $minimal,
        ?Closure $output = null,
    ): void
    {
        $this->scaffoldLibrary(
            $libName,
            $withCommands,
            $withRoutes,
            $withViews,
            $withConfigFile,
            $withMigrations,
            $minimal,
        );

        $this->bootstrappingLibrary($libName, $minimal);

        $this->registerLibrary($libName, $output);
    }

    private function scaffoldLibrary(
        string $libName,
        bool $withCommands,
        bool $withRoutes,
        bool $withViews,
        bool $withConfigFile,
        bool $withMigrations,
        bool $minimal,
    ): void
    {
        $laravelVersion = InstalledVersions::getPrettyVersion('laravel/framework');

        $this->generator->generate(
            new Tree([
                new Folder($libName, new Tree([
                    new Folder('src', new Tree([
                        new When(
                            $withCommands,
                            new Folder('Commands', new Tree([
                                new File('.gitkeep'),
                            ]))
                        ),
                        new When(
                            $withRoutes,
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
                            ]))
                        ),
                        new Folder('Providers', new Tree([
                            new ServiceProviderFile(
                                $libName,
                                $withRoutes,
                                $withViews,
                                $withConfigFile,
                                $withMigrations,
                            )
                        ])),
                        new When(
                            $withMigrations,
                            new Folder('Models', new Tree([
                                new File('.gitkeep'),
                            ]))
                        )
                    ])),
                    new When(
                        $withRoutes,
                        new Folder('routes', new Tree([
                            new File("web.php", "<?php\n\nuse Illuminate\Support\Facades\Route;\n")
                        ]))
                    ),
                    new When(
                        $withConfigFile,
                        new Folder('config', new Tree([
                            new File("$libName.php", "<?php\n\nreturn [];")
                        ]))
                    ),
                    new When(
                        $withViews,
                        new Folder('resources', new Tree([
                            new Folder('views', new Tree([
                                new File('.gitkeep'),
                            ])),
                            new Folder('lang', new Tree([
                                new File('.gitkeep'),
                            ])),
                        ]))
                    ),
                    new When(
                        $withMigrations,
                        new Folder('database', new Tree([
                            new Folder('migrations', new Tree([
                                new File('.gitkeep'),
                            ]))
                        ]))
                    ),
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
                    new When(
                        !$minimal,
                        new Tree([
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
                        ])
                    ),
                ])),
            ]),
            $this->baseDirectory,
        );
    }

    private function bootstrappingLibrary(string $libName, bool $minimal): void
    {
        $libDir = $this->baseDirectory.DIRECTORY_SEPARATOR.$libName;
        $this->composer->setWorkingPath($libDir)
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
        if (!$minimal) {
            $this->composer->dumpAutoloads();
        }
    }

    private function registerLibrary(string $libName, ?Closure $output = null): void
    {
        $this->composer->setWorkingPath($this->basePath)
            ->modify(function (array $file) use ($libName) {
                $repos = array_filter($file['repositories'] ?? [],
                    fn ($item) => ($item['url'] ?? null) === "libs/$libName");
                if (empty($repos)) {
                    $file['repositories'][] = [
                        'type' => 'path',
                        'url' => "libs/$libName",
                        'symlink' => true,
                    ];
                }
                return $file;
            });
        $this->composer->requirePackages(["libs/$libName:@dev"], output: $output);
    }
}
