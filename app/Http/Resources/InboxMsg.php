<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InboxMsg extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $is_me = auth()->user()->id ==  $this->user_id ? 'yes' : 'no';

        return [
            'id' => $this->id,
            'status' => $this->status,
            'user_id' => $this->user_id,
            'is_me' => $is_me,
            'message' => $this->message,
            'created_at' => $this->created_at
        ];
    }
}
