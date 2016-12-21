<?php

namespace FTumiwan\DomainCore;

use FTumiwan\DomainCore\Command\DomainModel;
use Illuminate\Support\ServiceProvider;

class DomainCoreServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if (file(__DIR__.'/../Routes.php')) {
            $this->loadRoutesFrom(__DIR__.'/../Routes.php');        
        }        
        $this->publishes([
            __DIR__.'/../../schema' => base_path('packages/ftumiwan/schema'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {        
        $this->app->singleton('command.ftumiwan.domainmodel',function()
        {
            return new DomainModel;
        });

        $this->commands('command.ftumiwan.domainmodel');
    }

    
}
