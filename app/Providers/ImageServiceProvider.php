<?php
declare(strict_types=1);
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Intervention\Image\ImageManager;

class ImageServiceProvider extends ServiceProvider
{
    /**
     * Binding name of the service container
     */
    protected const BINDING = 'image';

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        // 
    }

    /**
     * Register the image service
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton($this::BINDING, function ($app) {
            return new ImageManager(config('image.driver'));
        });
    }
}