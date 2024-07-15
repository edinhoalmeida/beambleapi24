<?php

namespace Babel\Providers;

use Illuminate\Support\ServiceProvider;

use Babel\Babel;

class BabelServiceProvider extends ServiceProvider
{
    public function register()
    {
        // $this->app->singleton(Babel::class, function () {
        //     return new Babel;
        // });
        $this->app->bind('babel', function () {
            return new Babel;
        });
    }

    public function boot()
    {
        //
    }

}
