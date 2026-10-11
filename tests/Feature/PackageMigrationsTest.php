<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use JeffersonGoncalves\PestPluginDatabaseMigrations\PackageMigrations;
use JeffersonGoncalves\PestPluginDatabaseMigrations\Tests\Fixtures\FixtureServiceProvider;

it('runs the migrations in the order declared by the service provider', function () {
    expect(DB::table('migrations')->orderBy('id')->pluck('migration')->all())->toBe([
        '0000_create_authors_table',
        '0001_create_posts_table',
        '0002_add_bio_to_authors',
    ]);

    expect(Schema::hasColumn('authors', 'bio'))->toBeTrue()
        ->and(Schema::hasColumn('posts', 'author_id'))->toBeTrue();
});

it('resolves both .php and .php.stub files', function () {
    $files = array_map('basename', PackageMigrations::files($this->app, FixtureServiceProvider::class));

    expect($files)->toBe([
        'create_authors_table.php.stub',
        'create_posts_table.php',
        'add_bio_to_authors.php.stub',
    ]);
});

it('removes stale staged migrations', function () {
    $dir = PackageMigrations::stage($this->app, FixtureServiceProvider::class);
    touch($dir.'/0099_old.php');

    PackageMigrations::stage($this->app, FixtureServiceProvider::class);

    expect(file_exists($dir.'/0099_old.php'))->toBeFalse();
});
