<?php

use ErickJMenezes\Modularavel\Scaffolding\Tree;
use ErickJMenezes\Modularavel\UseCases\GenerateLibrary;
use Illuminate\Support\Composer;
use ErickJMenezes\Modularavel\Scaffolding\Generator;

it('must generate the libraries', function () {
    $composer = Mockery::mock(Composer::class);
    $generator = Mockery::mock(Generator::class);
    $baseDir = 'any_folder';
    $libName = 'test-lib';

    $generator->shouldReceive('generate')
        ->with(Mockery::type(Tree::class), $baseDir);

    $composer->shouldReceive('setWorkingPath')
        ->with(Mockery::type('string'))
        ->andReturnSelf();
    $composer->shouldReceive('modify')
        ->withAnyArgs()
        ->andReturn();
    $composer->shouldReceive('dumpAutoloads')
        ->withAnyArgs()
        ->andReturn(0);

    $composer->shouldReceive('setWorkingPath')
        ->with(Mockery::type('string'))
        ->andReturnSelf();
    $composer->shouldReceive('modify')
        ->withAnyArgs()
        ->andReturn();
    $composer->shouldReceive('requirePackages')
        ->withSomeOfArgs(["libs/$libName:@dev"])
        ->andReturn(true);

    $useCase = new GenerateLibrary($generator, $composer, $baseDir, '.');

    $useCase->generate(
        $libName,
        true,
        true,
        true,
        true,
        true,
        false,
    );

    expect(true)->toBeTrue();
});
