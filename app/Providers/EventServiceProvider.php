<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

use App\Events\MapChanged;
use App\Events\BeamerAccept;
use App\Events\BeamerReject;
use App\Events\CallTimer;
use App\Events\ClientAsk;
use App\Events\ProductOffered;

use App\Listeners\SendPushNotificationMap;
use App\Listeners\SendBeamerAccept;
use App\Listeners\SendBeamerReject;
use App\Listeners\SendCallTimer;
use App\Listeners\SendClientAsk;
use App\Listeners\SendProductOffered;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        MapChanged::class => [
            SendPushNotificationMap::class,
        ],
        BeamerAccept::class => [
            SendBeamerAccept::class,
        ],
        BeamerReject::class => [
            SendBeamerReject::class,
        ],
        ClientAsk::class => [
            SendClientAsk::class,
        ],
        CallTimer::class => [
            SendCallTimer::class,
        ],
        ProductOffered::class => [
            SendProductOffered::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
