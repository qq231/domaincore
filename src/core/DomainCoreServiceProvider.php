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
        //
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
