<?php
namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Events\MapChanged;
use App\Libs\FirebasePN;

class SendPushNotificationMap
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function handle(MapChanged $event)
    {
        // not used yet $event->user_track
        $firebasePN = new FirebasePN();
        $firebasePN->send_map_changed([]);
    }
}
