<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use DB;

class VideocallAudio extends Model
{
    use HasFactory;

    protected $table = 'videocalls_audio';

    protected $fillable = [
        'call_id', 'side', 'language_code', 'from_text', 'file_name'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

}
