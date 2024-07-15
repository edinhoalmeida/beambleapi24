<?php

namespace Shipping\Providers;

use Illuminate\Support\ServiceProvider;

use Shipping\Shipping;

class ShippingServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('shipping', function () {
            return new Shipping;
        });
    }

    public function boot()
    {
        //
    }

}
