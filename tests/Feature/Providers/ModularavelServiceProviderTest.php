<?php

use ErickJMenezes\Modularavel\Scaffolding\Generator;
use ErickJMenezes\Modularavel\UseCases\GenerateLibrary;

describe('Service provider test', function () {
   it('has configs', function () {
       expect(config('modularavel'))
           ->toBeArray()
           ->not->toBeEmpty();
   });

   it('must bound classes', function (string $className) {
       expect(app()->bound($className))->toBeTrue();
   })->with([
       Generator::class,
       GenerateLibrary::class,
   ]);
});
