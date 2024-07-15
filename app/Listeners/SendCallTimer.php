<?php
namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Events\CallTimer;
use App\Libs\FirebasePN;
use App\Models\User;

class SendCallTimer
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

    public function handle(CallTimer $event)
    {
        $beamer = User::find($event->video_call->beamer_id);
        $client = User::find($event->video_call->client_id);
        if($event->action_name == 'timer_start'){
            $devices =  $client->devices;
            if(!empty($devices)){
                foreach($devices as $device){
                    $firebasePN = new FirebasePN();
                    $firebasePN->send_call_timer($device->firebase_token, $beamer->name, 
                    $title= "Beamer wants to start billed time.", 
                    $event->video_call, $event->action_name);
                }
            }
        } elseif($event->action_name == 'timer_accept'){
            $devices =  $beamer->devices;
            if(!empty($devices)){
                foreach($devices as $device){
                    $firebasePN = new FirebasePN();
                    $firebasePN->send_call_timer($device->firebase_token, $client->name, 
                    $title= "Customer authorized the start of the charged time.", 
                    $event->video_call, $event->action_name);
                }
            }
        } elseif($event->action_name == 'timer_reject'){
            $devices =  $beamer->devices;
            if(!empty($devices)){
                foreach($devices as $device){
                    $firebasePN = new FirebasePN();
                    $firebasePN->send_call_timer($device->firebase_token, $client->name, 
                        $title= "Customer denied the start of the charged time.", 
                        $event->video_call, $event->action_name);
                }
            }
        }
        // return false;
    }
}
