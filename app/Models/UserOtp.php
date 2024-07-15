<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Carbon\Carbon;
/*
CREATE TABLE `user_otp` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hash_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
*/
class UserOtp extends Model
{
    protected $table = 'user_otp';

    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'email', 'hash_number', 'status', 'deleted_at', 'created_at'
    ];

    protected $hidden = [
        'created_at', 'updated_at', 'user_id', 'deleted_at'
    ];

    public static function expires_otp(){
        $imutable = Carbon::now();
        $time_max_seconds = 1200; # 20 minutes
        $mutable = Carbon::now();
        $mutable->subSeconds($time_max_seconds);
        $expired = 
            UserOtp::where('created_at', '<', $mutable)
            ->where('status', 'valid')
            ->get();
        foreach($expired as $otp){
            $otp->status = 'expired';
            $otp->deleted_at = $imutable;
            $otp->save();
        }
        return True;
    }

    public static function generate_otp($email){
        $has_line = 
            UserOtp::where('email', '=', $email)
            ->first();
        $open_number = str(rand(1001, 9999));
        $hash_number = md5($open_number);
        $imutable = Carbon::now();
        if(!empty($has_line)){
            $has_line->hash_number = $hash_number;
            $has_line->status = 'valid';
            $has_line->created_at = $imutable;
            $has_line->save();
        } else {
            $data = ['email'=>$email,'hash_number'=>$hash_number,'status'=>'valid', 'created_at'=>$imutable];
            UserOtp::create($data);
        }
        return $open_number;
    }

    public static function is_valid($email, $otp_number){
        $has_line = 
            UserOtp::where('email', '=', $email)
            ->where('status', '=', 'valid')
            ->where('hash_number', '=', md5($otp_number))
            ->first();
        
        if(!empty($has_line)){
            $has_line->status = 'verified';
            $has_line->save();
            return True;
        }
        
        return False;
    }
}
