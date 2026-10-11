# Changelog

All notable changes to `pest-plugin-database-migrations` will be documented in this file.

## 1.0.0 - 2026-10-11

First release.

- `LoadsPackageMigrations` trait for Orchestra Testbench: `$this->loadPackageMigrations(Provider::class)` loads a package's migrations in the order declared by its `spatie/laravel-package-tools` ServiceProvider (`hasMigrations()`), instead of alphabetical order.
- Resolves `.php` and `.php.stub` migrations, supports multiple providers in sequence.
- `PackageMigrations::files()` / `PackageMigrations::stage()` for use without the trait.
- PHP 8.2+, Laravel 11 / 12 / 13, Pest 3 / 4.

**Full Changelog**: https://github.com/jeffersongoncalves/pest-plugin-database-migrations/commits/1.0.0
