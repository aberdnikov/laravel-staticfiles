<?php namespace Larakit\Larastatic;

use Illuminate\Support\ServiceProvider;

class LarastaticServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;


    public function boot() {
        $this->app->bind('larakit:static-deploy', function ($app) {
            return new CommandDeploy();
        });
        $this->app->bind('larakit:static-flush', function ($app) {
            return new CommandFlush();
        });
        $this->commands([
            'larakit:static-deploy',
            'larakit:static-flush',
        ]);
        $this->package('larakit/larastatic');
        if(class_exists('\Twig')){
            \Twig::addExtension(new \Larakit\Larastatic\Twig);
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        //
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() {
        return [];
    }

}
