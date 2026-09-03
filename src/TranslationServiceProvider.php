<?php

declare(strict_types=1);

namespace Karnoweb\Translation;

use Illuminate\Support\ServiceProvider;

class TranslationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/translation.php', 'translation');

        $this->app->singleton('translation', fn () => new Translation);
        $this->app->singleton(Translation::class, fn ($app) => $app->make('translation'));
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/translation.php' => config_path('translation.php'),
            ], 'translation-config');

            // Keep fixed 2022_* filenames so published migrations run early and keep stable names.
            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ], 'translation-migrations');
        }
    }
}
