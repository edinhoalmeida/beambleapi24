<?php

namespace App\Models\Inbox;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/*
inbox_messages
    id
    inbox_id
    user_id
    status
    message
16  deleted_at  timestamp           Sim NULL
17  created_at  timestamp           Sim NULL
18  updated_at  timestamp           Sim NULL
*/
class InboxMessages extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'inbox_id', 'user_id', 'status', 'message'
    ];

    protected $hidden = [
        'updated_at', 'deleted_at'
    ];

    public function inbox()
    {
        return $this->belongsTo(Inbox::class);
    }
}
