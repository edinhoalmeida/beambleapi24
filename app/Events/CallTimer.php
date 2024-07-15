<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use App\Models\Videocall;

use Exception;

class CallTimer
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $video_call;
    public $action_name;

    private $action_enum = ['timer_start','timer_accept','timer_reject'];

    public function __construct(Videocall $video_call, string $action_name)
    {
        if( ! in_array($action_name, $this->action_enum)){
            throw new Exception('Action_name must be one of: ' . implode(', ', $this->action_enum));
        }
        $this->video_call = $video_call;
        $this->action_name = $action_name;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    // public function broadcastOn()
    // {
    //     return new PrivateChannel('channel-name');
    // }
}
