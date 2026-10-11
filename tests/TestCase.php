<?php

namespace JeffersonGoncalves\PestPluginDatabaseMigrations\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use JeffersonGoncalves\PestPluginDatabaseMigrations\LoadsPackageMigrations;
use JeffersonGoncalves\PestPluginDatabaseMigrations\Tests\Fixtures\FixtureServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    use LoadsPackageMigrations;
    use RefreshDatabase;

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadPackageMigrations(FixtureServiceProvider::class);
    }
}
