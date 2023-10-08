<?php

namespace ErickJMenezes\Modularavel\Scaffolding\Stubs;

use ErickJMenezes\Modularavel\Scaffolding\File;

readonly class ServiceProviderFile extends AbstractStub
{
    public function __construct(string $libName)
    {
        $studlyLibName = str($libName)->studly();

        $serviceProviderName = (string) $studlyLibName
            ->append('ServiceProvider');

        $namespace = (string) $studlyLibName
            ->prepend('Libs\\')
            ->append('\\Providers');

        parent::__construct("$serviceProviderName.php", 'service-provider.stub', [
            'NAMESPACE' => $namespace,
            'SERVICE_PROVIDER_NAME' => $serviceProviderName,
            'LIB_NAME' => $libName,
            'STUDLY_LIB_NAME' => $studlyLibName,
        ]);
    }
}
