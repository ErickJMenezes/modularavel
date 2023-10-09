<?php

use ErickJMenezes\Modularavel\Scaffolding\Generator;

describe('Service provider test', function () {
   it('has configs', function () {
       expect(config('modularavel'))
           ->toBeArray()
           ->not->toBeEmpty();
   });

   it('must bound the generator', function () {
       expect(app()->bound(Generator::class))->toBeTrue();
   });
});
