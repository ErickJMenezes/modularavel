<?php

use ErickJMenezes\Modularavel\Scaffolding\Command;
use ErickJMenezes\Modularavel\Scaffolding\File;
use ErickJMenezes\Modularavel\Scaffolding\Folder;
use ErickJMenezes\Modularavel\Scaffolding\Generator;
use ErickJMenezes\Modularavel\Scaffolding\Stubs\PestMainFile;
use ErickJMenezes\Modularavel\Scaffolding\Stubs\ServiceProviderFile;
use ErickJMenezes\Modularavel\Scaffolding\Stubs\TestCaseFile;
use ErickJMenezes\Modularavel\Scaffolding\Tree;

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

    it('must generate stubs', function () use ($generator, $trashcan) {
        $generator->generate(
            new Tree([
                new PestMainFile('test'),
                new ServiceProviderFile('test'),
                new TestCaseFile('test'),
            ]),
            $trashcan,
        );

        expect("$trashcan/Pest.php")
            ->toBeFile()
            ->and("$trashcan/TestServiceProvider.php")
            ->toBeFile()
            ->and(file_get_contents("$trashcan/TestServiceProvider.php"))
            ->toContain('TestServiceProvider', 'Libs\\Test\\Providers')
            ->and("$trashcan/TestCase.php")
            ->toBeFile();
    });
})->skipOnWindows();
