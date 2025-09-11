<?php

namespace Attargah\AntiScam;


use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Blade;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;


class AntiScamServiceProvider extends PackageServiceProvider
{
    public static string $name = 'anti-scam';

    public static string $viewNamespace = 'anti-scam';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name(static::$name)
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('attargah/anti-scam');
            });


        $configFileName = $package->shortName();

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

        if (file_exists($package->basePath('/../database/migrations'))) {
            $package->hasMigrations($this->getMigrations());
        }
        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }

    }

    public function packageRegistered(): void {}

    public function packageBooted(): void
    {

        Blade::directive('protect', function ($expression) {
            $view = 'anti-scam::directives.protect';
            if (view()->exists('directives.protect')) {
                $view = 'directives.protect';
            }

            return "<?php echo view('{$view}', ['identity' => {$expression}])->render(); ?>";
        });

        // Handle Stubs
        if (app()->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/anti-scam/{$file->getFilename()}"),
                ], 'anti-scam-stubs');
            }
        }




    }


    /**
     * @return array<string>
     */
    protected function getMigrations(): array
    {
        return [
            'create_anti_scam_table',
        ];
    }
}
