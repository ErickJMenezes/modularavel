<?php

namespace ErickJMenezes\Modularavel\Scaffolding;

readonly class ServiceProviderFile extends File
{
    public function __construct(string $libName)
    {
        $studlyLibName = str($libName)->studly();

        $serviceProviderName = (string) $studlyLibName
            ->append('ServiceProvider');

        $namespace = (string) $studlyLibName
            ->prepend('Libs\\')
            ->append('\\Providers');

        $fileContents = str_replace(
            ['{{NAMESPACE}}', '{{SERVICE_PROVIDER_NAME}}', '{{LIB_NAME}}', '{{STUDLY_LIB_NAME}}'],
            [$namespace, $serviceProviderName, $libName, (string)$studlyLibName],
            file_get_contents(__DIR__.'/../../stubs/service-provider.stub'),
        );

        parent::__construct("$serviceProviderName.php", $fileContents);
    }
}
