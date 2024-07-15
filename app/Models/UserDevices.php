<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserDevices extends Model
{
    use HasFactory;

    use SoftDeletes;
    protected $table = 'user_devices';

    protected $fillable = [
        'user_id', 'firebase_token'
    ];

    protected $hidden = [
        'created_at', 'updated_at', 'user_id', 'deleted_at'
    ];

    public static function add_device($user_id, $firebase_token)
    {
        $devices = UserDevices::where('user_id', $user_id)
                ->where('firebase_token', $firebase_token);
        if(! $devices->exists()) {
            UserDevices::create(['user_id' => $user_id, 'firebase_token' => $firebase_token]);
        }
    }

    public static function change_device($user_id, $firebase_token)
    {
        self::remove_all_devices($user_id);
        self::add_device($user_id, $firebase_token);

    }

    public static function remove_all_devices($user_id)
    {
        UserDevices::where('user_id', $user_id)->delete();
    }
    
}
