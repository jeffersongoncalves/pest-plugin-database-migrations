<?php

namespace JeffersonGoncalves\PestPluginDatabaseMigrations\Tests\Fixtures;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FixtureServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        // Alphabetical order would run "create_posts_table" before "create_authors_table" and
        // "add_bio_to_authors" first of all, which fails.
        $package
            ->name('fixture')
            ->hasMigrations([
                'create_authors_table',
                'create_posts_table',
                'add_bio_to_authors',
            ]);
    }
}
