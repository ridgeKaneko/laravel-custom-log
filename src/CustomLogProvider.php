<?php

namespace CustomLog;

use CustomLog\Commands\MakeDriver;
use CustomLog\Commands\MakeProcessor;
use Illuminate\Support\ServiceProvider;

class CustomLogProvider extends ServiceProvider
{
    const CONFIG_PATH = __DIR__.'/config.php';
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->publishes([self::CONFIG_PATH => config_path('customlog.php')],'custom-log');
        $this->mergeConfigFrom(self::CONFIG_PATH,'customlog');

        $this->commands([MakeDriver::class, MakeProcessor::class]);

        $this->app->singleton(DBLogService::class,function () {
            return new DBLogService(config('customlog.db'));
        });
        $this->app->extend('log',function ($origin,$app) {
            return new CustomLogManager($app);
        });
    }
}
