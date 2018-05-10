<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class CommandServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('command.patch.hello', function () {
            return new \App\Console\Commands\Hello;
        });

        $this->commands(
            'command.patch.hello'
        );

    }
}