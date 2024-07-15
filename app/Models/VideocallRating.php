<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use DB;

class VideocallRating extends Model
{
    use HasFactory;

    protected $table = 'videocalls_rating';

    protected $fillable = [
        'call_id', 'side', 'rating','evaluation'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

}
