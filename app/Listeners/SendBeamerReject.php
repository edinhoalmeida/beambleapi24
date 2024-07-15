<?php
namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Events\BeamerReject;
use App\Libs\FirebasePN;
use App\Libs\FirebaseDB;
use App\Models\User;

class SendBeamerReject
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

    public function handle(BeamerReject $event)
    {
        $beamer = User::find($event->video_call->beamer_id);
        $client = User::find($event->video_call->client_id);
        $devices =  $client->devices;
        if(!empty($devices)){
            foreach($devices as $device){
                $firebasePN = new FirebasePN();
                $firebasePN->send_beamer_reject($device->firebase_token, $beamer->name, $title= "Beamer declined the call.", $event->video_call);
            }
        }

        $send_data = [
            'event' => 'beamer_reject',
            'url' => 'beamble://home',
            'callId' => $event->video_call->id,
            'client_id' => $event->video_call->client_id,
            'beamer_id' => $event->video_call->beamer_id
        ];
        // redundance check on FBDB
        try {
            $persist = [
                'user_id' => (int) $event->video_call->client_id,
                'videocall' => $send_data
            ];
            FirebaseDB::getInstance()->askcall_update($persist);
        } catch (\Exception $e) {
            dblog('persist_askcall_db', $e->getMessage());
        }
        // return false;
    }
}
