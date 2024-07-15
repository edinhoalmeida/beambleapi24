<?php
declare(strict_types=1);
namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ImageProcessorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Service\ImageProcessorInterface', 'App\Service\ImageProcessor');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
