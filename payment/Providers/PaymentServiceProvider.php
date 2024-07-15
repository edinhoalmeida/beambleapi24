<?php

namespace Payment\Providers;

use Illuminate\Support\ServiceProvider;

use Payment\Payment;

class PaymentServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('payment', function () {
            return new Payment;
        });
    }

    public function boot()
    {
        //
    }

}
