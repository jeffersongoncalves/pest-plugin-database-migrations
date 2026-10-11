<?php

namespace JeffersonGoncalves\PestPluginDatabaseMigrations;

use Spatie\LaravelPackageTools\PackageServiceProvider;

/**
 * For Orchestra Testbench test cases.
 *
 *   protected function defineDatabaseMigrations(): void
 *   {
 *       $this->loadPackageMigrations(MyPackageServiceProvider::class);
 *   }
 */
trait LoadsPackageMigrations
{
    /**
     * @param  class-string<PackageServiceProvider>  ...$providers
     */
    protected function loadPackageMigrations(string ...$providers): void
    {
        $this->loadMigrationsFrom(PackageMigrations::stage($this->app, ...$providers));
    }
}
