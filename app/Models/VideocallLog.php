<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use DB;

class VideocallLog extends Model
{
    use HasFactory;

    protected $table = 'videocalls_logs';

    protected $fillable = [
        'call_id', 'side', 'status'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    protected $casts = [
        'created_at' => 'datetime:U',
    ];

    public static function call_duration_seconds($call_id){
        $dt_ini = DB::select("SELECT 
            UNIX_TIMESTAMP(created_at) as created_at
            FROM videocalls_logs 
            WHERE 
                side = ?
                AND 
                status = ?
                AND 
                call_id = ?
            ORDER BY created_at ASC LIMIT 1", ['client','MeetingJoined',$call_id]);

        $dt_end = DB::select("SELECT 
            UNIX_TIMESTAMP(created_at) as created_at
            FROM videocalls_logs 
            WHERE 
                side = ?
                AND 
                status = ?
                AND 
                call_id = ?
            ORDER BY created_at DESC LIMIT 1", ['client','MeetingLeft',$call_id]);

        if(empty($dt_ini) || empty($dt_end)){
            return -1;
        }
        return (int) $dt_end[0]->created_at - (int) $dt_ini[0]->created_at;
    }

}
