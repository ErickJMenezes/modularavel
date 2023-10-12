<?= "<?php\n" ?>

namespace Libs\<?= getenv('STUDLY_LIB_NAME') ?>\Tests;

use Libs\<?= getenv('STUDLY_LIB_NAME') ?>\Providers\<?= getenv('STUDLY_LIB_NAME') ?>ServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            <?= getenv('STUDLY_LIB_NAME') ?>ServiceProvider::class,
            // Register other Service Providers here.
        ];
    }
}
