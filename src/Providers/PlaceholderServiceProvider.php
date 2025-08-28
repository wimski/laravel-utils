<?php

declare(strict_types=1);

namespace Wimski\LaravelPackageTemplate\Providers;

use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\ServiceProvider;
use Wimski\LaravelPackageTemplate\Console\Commands\PlaceholderCommand;

class PlaceholderServiceProvider extends ServiceProvider
{
    public const string PACKAGE = 'placeholder';

    public function boot(): void
    {
        $this->loadResources();
        $this->publishResources();

        if ($this->app->runningInConsole()) {
            $this->commands([
                PlaceholderCommand::class,
            ]);
        }

        AboutCommand::add(self::PACKAGE, fn (): array => [
            //
        ]);
    }

    protected function loadResources(): void
    {
        $this->loadRoutes();
        $this->loadTranslations();
        $this->loadViews();
    }

    protected function publishResources(): void
    {
        $this->publishConfig();
        $this->publishMigrations();
        $this->publishTranslations();
        $this->publishViews();
    }

    protected function loadRoutes(): void
    {
        $this->loadRoutesFrom($this->resourcesPath('routes/web.php'));
    }

    protected function loadTranslations(): void
    {
        $this->loadTranslationsFrom($this->resourcesPath('lang'), self::PACKAGE);
    }

    protected function loadViews(): void
    {
        $this->loadViewsFrom($this->resourcesPath('views'), self::PACKAGE);
    }

    protected function publishConfig(): void
    {
        $this->publishes([
            $this->resourcesPath('config/' . self::PACKAGE . '.php') => config_path(self::PACKAGE . '.php'),
        ]);
    }

    protected function publishMigrations(): void
    {
        $this->publishesMigrations([
            $this->resourcesPath('database/migrations') => database_path('migrations'),
        ]);
    }

    protected function publishTranslations(): void
    {
        $this->publishes([
            $this->resourcesPath('lang') => lang_path('vendor/' . self::PACKAGE),
        ]);
    }

    protected function publishViews(): void
    {
        $this->publishes([
            $this->resourcesPath('views') => resource_path('views/vendor/' . self::PACKAGE),
        ]);
    }

    protected function resourcesPath(string $path): string
    {
        return __DIR__ . "/../../resources/{$path}";
    }
}
