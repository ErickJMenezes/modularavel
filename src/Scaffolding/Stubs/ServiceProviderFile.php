<?php

namespace ErickJMenezes\Modularavel\Scaffolding\Stubs;

readonly class ServiceProviderFile extends AbstractStub
{
    public function __construct(
        string $libName,
        bool $withRoutes,
        bool $withViews,
        bool $withConfigFile,
        bool $withMigrations,
    )
    {
        $studlyLibName = str($libName)->studly();

        parent::__construct("{$studlyLibName}ServiceProvider.php", 'service-provider.stub.php', [
            'LIB_NAME' => $libName,
            'STUDLY_LIB_NAME' => $studlyLibName,
            'WITH_ROUTES' => $withRoutes,
            'WITH_VIEWS' => $withViews,
            'WITH_CONFIG' => $withConfigFile,
            'WITH_MIGRATIONS' => $withMigrations,
        ]);
    }
}
