<?php namespace Bestmomo\Filemanager;
 
use Illuminate\Support\ServiceProvider;
 
class FilemanagerServiceProvider extends ServiceProvider{
 
 
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;
 
    public function boot()
    {
        // Assets
        $this->publishes([
            __DIR__.'/../public' => public_path(),
        ], 'public');
    }
 
    public function register() {}
 
}