<?php

use ErickJMenezes\Modularavel\Scaffolding\Command;
use ErickJMenezes\Modularavel\Scaffolding\File;
use ErickJMenezes\Modularavel\Scaffolding\Folder;
use ErickJMenezes\Modularavel\Scaffolding\Generator;
use ErickJMenezes\Modularavel\Scaffolding\Stubs\PestMainFile;
use ErickJMenezes\Modularavel\Scaffolding\Stubs\ServiceProviderFile;
use ErickJMenezes\Modularavel\Scaffolding\Stubs\TestCaseFile;
use ErickJMenezes\Modularavel\Scaffolding\Tree;
use ErickJMenezes\Modularavel\Scaffolding\When;

describe('Generator Test', function () {
    $generator = new Generator();
    $trashcan = __DIR__.'/../../../vendor/__trashcan';
    beforeEach(fn () => mkdir($trashcan, 0755, true));
    afterEach(fn () => system("rm -rf $trashcan"));

    it('must create generic files', function () use ($trashcan, $generator) {
        $generator->generate(
            new Tree([
                new File('file.txt', 'content'),
            ]),
            $trashcan,
        );

        $generatedFile = "$trashcan/file.txt";
        expect($generatedFile)
            ->toBeFile()
            ->toBeReadableFile()
            ->toBeWritableFile()
            ->and(file_get_contents($generatedFile))
            ->toBe('content');
    });

    it('must create directories', function () use ($trashcan, $generator) {
        $generator->generate(
            new Tree([
                new Folder('foo', new Tree([
                    new Folder('bar'),
                ])),
            ]),
            $trashcan,
        );

        expect("$trashcan/foo")
            ->toBeDirectory()
            ->and("$trashcan/foo/bar")
            ->toBeDirectory();
    });

    it('must execute commands inside directory', function () use ($trashcan, $generator) {
        $generator->generate(
            new Tree([
                new Folder('foo', new Tree([
                    new Command(['touch', 'file.txt']),
                ]))
            ]),
            $trashcan,
        );

        expect("$trashcan/foo")
            ->toBeDirectory()
            ->and("$trashcan/foo/file.txt")
            ->toBeFile();
    });

    it('must consider conditional directives', function () use ($trashcan, $generator) {
        $generator->generate(new Tree([
            new When(true, new File('test1.txt')),
            new When(false, new File('test2.txt')),
        ]), $trashcan);

        expect("$trashcan/test1.txt")
            ->toBeFile()
            ->and("$trashcan/test2.txt")
            ->not->toBeFile();
    });

    it('must allow top level subtrees', function () use ($generator, $trashcan) {
        $generator->generate(
            new Tree([
                new Tree([
                    new Tree([
                        new File('test.txt'),
                    ])
                ])
            ]),
            $trashcan,
        );

        expect("$trashcan/test.txt")
            ->toBeFile();
    });

    it('must generate stubs for the pest main file', function () use ($generator, $trashcan) {
        $generator->generate(
            new Tree([
                new PestMainFile('test'),
            ]),
            $trashcan,
        );

        expect("$trashcan/Pest.php")
            ->toBeFile();
    });

    it('must generate stubs for the service provider file', function (
        array $args,
        array $toContain,
        array $notToContain,
    ) use ($generator, $trashcan) {
        $generator->generate(
            new Tree([
                new ServiceProviderFile('test', ...$args),
            ]),
            $trashcan,
        );

        expect("$trashcan/TestServiceProvider.php")
            ->toBeFile()
            ->and(file_get_contents("$trashcan/TestServiceProvider.php"))
            ->when(!empty($toContain), fn ($e) => $e->toContain(...$toContain))
            ->when(!empty($notToContain), fn ($e) => $e->not->toContain(...$notToContain));
    })->with([
        'with views and routes' => [
            [true, true, true, true],
            [
                'loadRoutesFrom',
                'loadViewsFrom',
                'loadTranslationsFrom',
                'componentNamespace',
                'mergeConfigFrom',
                'loadMigrationsFrom',
            ],
            [],
        ],
        'without views and routes' => [
            [false, false, false, false],
            [],
            [
                'loadRoutesFrom',
                'loadViewsFrom',
                'loadTranslationsFrom',
                'componentNamespace',
                'mergeConfigFrom',
                'loadMigrationsFrom'
            ],
        ],
        'only routes' => [
            [true, false, false, false],
            ['loadRoutesFrom'],
            ['loadViewsFrom', 'loadTranslationsFrom', 'componentNamespace', 'mergeConfigFrom'],
        ],
        'only views' => [
            [false, true, false, false],
            ['loadViewsFrom', 'loadTranslationsFrom', 'componentNamespace'],
            ['loadRoutesFrom', 'mergeConfigFrom'],
        ],
        'only configs' => [
            [false, false, true, false],
            ['mergeConfigFrom'],
            ['loadViewsFrom', 'loadTranslationsFrom', 'componentNamespace', 'loadRoutesFrom'],
        ],
        'only migrations' => [
            [false, false, false, true],
            ['loadMigrationsFrom'],
            ['loadViewsFrom', 'loadTranslationsFrom', 'componentNamespace', 'loadRoutesFrom', 'mergeConfigFrom'],
        ],
    ]);

    it('must generate stubs for the test case file', function () use ($generator, $trashcan) {
        $generator->generate(
            new Tree([
                new TestCaseFile('test'),
            ]),
            $trashcan,
        );

        expect("$trashcan/TestCase.php")
            ->toBeFile();
    });
})->skipOnWindows();
