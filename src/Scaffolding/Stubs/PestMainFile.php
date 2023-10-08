<?php

namespace ErickJMenezes\Modularavel\Scaffolding\Stubs;

readonly class PestMainFile extends AbstractStub
{
    public function __construct(string $libName)
    {
        parent::__construct('Pest.php', 'pest-main-file.stub', [
            'STUDLY_LIB_NAME' => str($libName)->studly(),
        ]);
    }
}
