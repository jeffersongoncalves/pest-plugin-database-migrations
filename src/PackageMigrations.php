<?php

namespace JeffersonGoncalves\PestPluginDatabaseMigrations;

use Illuminate\Contracts\Foundation\Application;
use RuntimeException;
use Spatie\LaravelPackageTools\PackageServiceProvider;

final class PackageMigrations
{
    /**
     * Migration files declared via hasMigration()/hasMigrations(), in declaration order,
     * resolved the same way spatie/laravel-package-tools does (".php", then ".php.stub").
     *
     * @param  class-string<PackageServiceProvider>  ...$providers
     * @return list<string>
     */
    public static function files(Application $app, string ...$providers): array
    {
        $files = [];

        foreach ($providers as $class) {
            $provider = new $class($app);
            $package = $provider->newPackage();
            $package->setBasePath((fn () => $this->getPackageBaseDir())->call($provider));
            $provider->configurePackage($package);

            foreach ($package->migrationFileNames as $name) {
                $file = $package->basePath("/../database/migrations/{$name}.php");

                if (! is_file($file)) {
                    $file .= '.stub';
                }

                if (! is_file($file)) {
                    throw new RuntimeException("Migration [{$name}] declared by [{$class}] not found.");
                }

                $files[] = $file;
            }
        }

        return $files;
    }

    /**
     * Copies the migrations into a temp directory with a numeric prefix, so the migrator
     * (which sorts by file name) runs them in declaration order. Returns that directory.
     *
     * @param  class-string<PackageServiceProvider>  ...$providers
     */
    public static function stage(Application $app, string ...$providers): string
    {
        $dir = sys_get_temp_dir().'/pest-plugin-database-migrations/'.md5(implode('|', $providers));

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $targets = [];

        foreach (self::files($app, ...$providers) as $index => $file) {
            $name = preg_replace('/\.php(\.stub)?$/', '', basename($file));
            $target = $targets[] = sprintf('%s/%04d_%s.php', $dir, $index, $name);

            // ponytail: only write on change, so parallel workers don't read half-copied files
            if (! is_file($target) || md5_file($target) !== md5_file($file)) {
                copy($file, $target);
            }
        }

        // Drop leftovers from a previous order/name so they don't run too.
        foreach (array_diff(glob($dir.'/*.php') ?: [], $targets) as $stale) {
            @unlink($stale);
        }

        return $dir;
    }
}
