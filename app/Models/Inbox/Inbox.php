<?php

namespace App\Models\Inbox;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/*
inbox
    id
    user_id_beamer
    user_id_client
    readed_beamer 0,1
    readed_client 0,1
    date
16  deleted_at  timestamp           Sim NULL
17  created_at  timestamp           Sim NULL
18  updated_at  timestamp           Sim NULL    

*/
class Inbox extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'user_id_beamer', 'user_id_client', 'readed_beamer', 'readed_client', 'date'
    ];

    protected $hidden = [
        'updated_at', 'deleted_at'
    ];

    public function messages()
    {
        return $this->hasMany(InboxMessages::class);
    }
}
