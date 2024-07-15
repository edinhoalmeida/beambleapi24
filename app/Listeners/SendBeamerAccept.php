<?php
namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Events\BeamerAccept;
use App\Libs\FirebasePN;
use App\Libs\FirebaseDB;
use App\Models\User;

class SendBeamerAccept
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

    public function handle(BeamerAccept $event)
    {
        dblog('SendBeamerAccept', $event->video_call->id);
        $beamer = User::find($event->video_call->beamer_id);
        $client = User::find($event->video_call->client_id);
        $devices =  $client->devices;
        dblog('devices', json_encode($devices));
        if(!empty($devices)){
            foreach($devices as $device){
                $firebasePN = new FirebasePN();
                $firebasePN->send_beamer_accept($device->firebase_token, $beamer->name, $title= "Beamer accpted your call!!!!!", $event->video_call);
            }
        } else {
            dblog('SendBeamerAccept client device not found ', $event->video_call->client_id);
        }

        $send_data = [
            'event' => 'beamer_accept',
            'callId' => $event->video_call->id,
            'client_id' => $event->video_call->client_id,
            'beamer_id' => $event->video_call->beamer_id,
            'url' => 'beamble://premeeting',
            'meeting_id' => $event->video_call->meeting_id,
            'meeting_object' => (string) $event->video_call->meeting_object,
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
