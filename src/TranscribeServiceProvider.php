<?php

namespace NicolasBeauvais\Transcribe;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;

class TranscribeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/transcribe.php' => config_path('transcribe.php'),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/transcribe.php', 'transcribe');

        $this->app->bind(Manager::class, function () {
            return new Manager(
                new Filesystem(),
                $this->app['config']['transcribe.path'],
                array_merge($this->app['config']['view.paths'], [$this->app['path']])
            );
        });

        $this->commands([
            \NicolasBeauvais\Transcribe\Commands\MissingCommand::class,
            \NicolasBeauvais\Transcribe\Commands\RemoveCommand::class,
            \NicolasBeauvais\Transcribe\Commands\TransCommand::class,
            \NicolasBeauvais\Transcribe\Commands\ShowCommand::class,
            \NicolasBeauvais\Transcribe\Commands\FindCommand::class,
            \NicolasBeauvais\Transcribe\Commands\SyncCommand::class,
            \NicolasBeauvais\Transcribe\Commands\RenameCommand::class,
        ]);
    }
}
