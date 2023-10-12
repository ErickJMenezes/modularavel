<?php

namespace ErickJMenezes\Modularavel\Scaffolding\Stubs;

readonly class TestCaseFile extends AbstractStub
{
    public function __construct(string $libName)
    {
        parent::__construct('TestCase.php', 'test-case.stub.php', [
            'STUDLY_LIB_NAME' => str($libName)->studly(),
        ]);
    }
}
