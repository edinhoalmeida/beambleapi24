<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSocialmedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'url'
    ];

    protected $hidden = [
        'created_at', 'updated_at', 'user_id', 'id'
    ];
}
