<?php

namespace Salmanbe\Resize;

use Illuminate\Support\ServiceProvider;

class ResizeServiceProvider extends ServiceProvider {

    public function boot() {

        $this->publishes([
            __DIR__ . '/config.php' => config_path('resize.php'),
        ]);
    }

    public function register() {

        $this->app->bind('resize', function($app) {
            return new FileName($app);
        });

        config(['config/resize.php']);
    }

}
