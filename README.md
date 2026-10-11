# Pest Plugin Database Migrations

Load a Laravel package's migrations in your tests in the **exact order declared by its ServiceProvider** (`spatie/laravel-package-tools` `hasMigrations()`), instead of the alphabetical order the migrator uses.

## The problem

Packages built with `spatie/laravel-package-tools` ship migrations without timestamps, often as `.php.stub`:

```php
$package->hasMigrations([
    'create_kb_categories_table',
    'create_kb_articles_table',
    'create_kb_article_versions_table',
]);
```

Laravel's migrator sorts files by name, so `create_kb_article_versions_table` runs before `create_kb_articles_table` (`_` < `s`) and foreign keys break on MySQL/PostgreSQL. The usual workaround is to copy stubs around and keep a second, hand-maintained list of migration names in `TestCase` that drifts every time a migration is added.

This package reads the list straight from the ServiceProvider, so the provider stays the single source of truth.

## Installation

```bash
composer require jeffersongoncalves/pest-plugin-database-migrations --dev
```

## Usage

In your Orchestra Testbench `TestCase`:

```php
use JeffersonGoncalves\KnowledgeBase\KnowledgeBaseServiceProvider;
use JeffersonGoncalves\PestPluginDatabaseMigrations\LoadsPackageMigrations;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    use LoadsPackageMigrations;
    use RefreshDatabase;

    protected function defineDatabaseMigrations(): void
    {
        $this->loadPackageMigrations(KnowledgeBaseServiceProvider::class);
    }
}
```

Works for the package's own test suite and for apps/packages testing against a package installed in `vendor/`. Pass several providers to load them in sequence (e.g. a package and the one it depends on):

```php
$this->loadPackageMigrations(BaseServiceProvider::class, ExtensionServiceProvider::class);
```

### How it works

1. Instantiates the provider and calls `configurePackage()` to read `migrationFileNames`.
2. Resolves each name to `database/migrations/{name}.php` or `{name}.php.stub`, the same way `laravel-package-tools` does.
3. Copies them to a temp directory as `0000_name.php`, `0001_name.php`, … and passes that directory to `loadMigrationsFrom()`.

Need the raw list? `PackageMigrations::files($app, Provider::class)` returns the ordered file paths.

## Limitations

- Only providers extending `Spatie\LaravelPackageTools\PackageServiceProvider` using `hasMigration()`/`hasMigrations()`. `discoversMigrations()` is not supported.

## Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
