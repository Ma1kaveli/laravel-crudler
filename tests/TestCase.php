<?php

namespace Tests;

use Crudler\Providers\CrudlerServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;
use Tests\Fakes\FakeResolver;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            CrudlerServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $databasePath = __DIR__ . '/temp/database.sqlite';

        if (! file_exists($databasePath)) {
            touch($databasePath);
        }

        Config::set('database.default', 'sqlite');
        Config::set('app.key', 'base64:'.base64_encode(random_bytes(32)));
        Config::set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => $databasePath,
            'prefix' => '',
        ]);
        Config::set(
            'core.form_dto.context_resolver',
            FakeResolver::class
        );
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->runTestMigrations();
        $this->setUpAuth();
    }

    /**
     * Миграции, нужные для тестов
     */
    protected function runTestMigrations(): void
    {
        Schema::dropAllTables();

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    /**
     * Хук для авторизации (по умолчанию — ничего)
     * Переопределяется в конкретных тестах
     */
    protected function setUpAuth(): void
    {
        // noop
    }
}
