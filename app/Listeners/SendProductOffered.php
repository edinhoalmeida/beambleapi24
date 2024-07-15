<?php
namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Events\ProductOffered;
use App\Libs\FirebasePN;
use App\Models\User;

class SendProductOffered
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

    public function handle(ProductOffered $event)
    {
        $call_id = $event->video_call->id;
        $beamer = User::find($event->video_call->beamer_id);
        $client = User::find($event->video_call->client_id);
        $devices =  $client->devices;
        if(!empty($devices)){
            foreach($devices as $device){
                dblog('product_offered', json_encode($event->video_call));
                $firebasePN = new FirebasePN();
                $firebasePN->send_product_offered($device->firebase_token, $beamer->name, $title= "You have new product offer!", $event->video_call, $event->product);
            }
        }
        // return false;
    }
}
