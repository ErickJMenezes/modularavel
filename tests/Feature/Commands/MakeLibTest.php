<?php

use Illuminate\Support\Composer;

use function Pest\Laravel\artisan;

describe('make:lib (testing with --minimal option)', function () {
    beforeEach(function () {
        $composer = app(Composer::class)->setWorkingPath(base_path());
        $composer->removePackages(['libs/test']);
        $composer->modify(function (array $file) {
            unset($file['repositories']);
            return $file;
        });
        system('rm -rf '.base_path('libs'));
    });

    it('must generate the structure for routes and controllers', function () {
        artisan('make:lib', [
            'name' => 'test',
            '--minimal' => true,
            '--with-routes' => true,
        ])
            ->assertOk()
            ->run();

        expect([
            base_path('libs/test/routes'),
            base_path('libs/test/src/Http/Controllers'),
            base_path('libs/test/src/Http/Resources'),
            base_path('libs/test/src/Http/Requests'),
        ])
            ->each->toBeDirectory()
            ->and(file_get_contents(base_path('libs/test/src/Providers/TestServiceProvider.php')))
            ->toContain('loadRoutesFrom');
    });

    it('must generate the structure for views', function () {
        artisan('make:lib', [
            'name' => 'test',
            '--minimal' => true,
            '--with-views' => true,
        ])
            ->assertOk()
            ->run();

        expect([
            base_path('libs/test/resources/views'),
            base_path('libs/test/resources/lang'),
        ])
            ->each->toBeDirectory()
            ->and(file_get_contents(base_path('libs/test/src/Providers/TestServiceProvider.php')))
            ->toContain('loadViewsFrom');
    });

    it('must generate the structure for commands', function () {
        artisan('make:lib', [
            'name' => 'test',
            '--minimal' => true,
            '--with-commands' => true,
        ])
            ->assertOk()
            ->run();

        expect([
            base_path('libs/test/src/Commands'),
        ])
            ->each->toBeDirectory();
    });

    it('must generate the structure for config file', function () {
        artisan('make:lib', [
            'name' => 'test',
            '--minimal' => true,
            '--with-config-file' => true,
        ])
            ->assertOk()
            ->run();

        expect([
            base_path('libs/test/config'),
        ])
            ->each->toBeDirectory()
            ->and(file_get_contents(base_path('libs/test/src/Providers/TestServiceProvider.php')))
            ->toContain('mergeConfigFrom');
    });

    it('must generate the structure for migrations', function () {
        artisan('make:lib', [
            'name' => 'test',
            '--minimal' => true,
            '--with-migrations' => true,
        ])
            ->assertOk()
            ->run();

        expect([
            base_path('libs/test/database/migrations'),
        ])
            ->each->toBeDirectory()
            ->and(file_get_contents(base_path('libs/test/src/Providers/TestServiceProvider.php')))
            ->toContain('loadMigrationsFrom');
    });
})->skipOnWindows();
