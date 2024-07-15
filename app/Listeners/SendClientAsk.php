<?php
namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Events\ClientAsk;
use App\Libs\FirebasePN;
use App\Libs\FirebaseDB;
use App\Models\User;

class SendClientAsk
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

    public function handle(ClientAsk $event)
    {
        $beamer_id = $event->video_call->beamer_id;
        $client_id = $event->video_call->client_id;
        $call_id = $event->video_call->id;

        $beamer = User::find($beamer_id);
        $client = User::find($client_id);

        $devices =  $beamer->devices;
        if(!empty($devices)){
            foreach($devices as $device){
                $firebasePN = new FirebasePN();
                $firebasePN->send_client_ask($device->firebase_token, $client->name, 
                    $title= "You have a call!!!!!", 
                    $call_id,
                    $beamer_id,
                    $client_id,
                );
            }
        }

        $notification_data = [
            'event' => 'client_ask',
            'callId' => $call_id,
            'beamer_id'=> $beamer_id,
            'client_id'=> $client_id,
            'url' => 'beamble://lobby/' . $call_id,
            'client_name' => $client->name,
        ];
        // redundance check on FBDB
        try {
            $persist = [
                'user_id' => (int) $beamer_id,
                'videocall' => $notification_data
            ];
            FirebaseDB::getInstance()->askcall_update($persist);
            dblog('persist_askcall_db', 'ok');
        } catch (\Exception $e) {
            dblog('persist_askcall_db', $e->getMessage());
        }
        // return false;
    }
}
