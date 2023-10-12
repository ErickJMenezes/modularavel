<?php

namespace ErickJMenezes\Modularavel\Scaffolding\Stubs;

readonly class ServiceProviderFile extends AbstractStub
{
    public function __construct(string $libName)
    {
        $studlyLibName = str($libName)->studly();

        parent::__construct("{$studlyLibName}ServiceProvider.php", 'service-provider.stub', [
            'LIB_NAME' => $libName,
            'STUDLY_LIB_NAME' => $studlyLibName,
        ]);
    }
}
