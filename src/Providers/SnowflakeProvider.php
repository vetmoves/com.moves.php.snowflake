<?php

namespace Moves\Snowflake\Providers;

use Carbon\Laravel\ServiceProvider;
use Moves\Snowflake\Console\Commands\ModelflakeMapCommand;

class SnowflakeProvider extends ServiceProvider
{
    public function boot()
    {
        $this->commands([
            ModelflakeMapCommand::class
        ]);

        $this->publishes([
            __DIR__ . '/../config/snowflake.php' => config_path('snowflake.php'),
        ]);
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/snowflake.php', 'snowflake'
        );
    }
}
