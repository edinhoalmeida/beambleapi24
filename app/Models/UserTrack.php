<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Libs\FirebaseDB;

use App\Models\UserTracking;
use App\Models\User;

/*

CREATE TABLE `users_track_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lat` float(11,8) DEFAULT NULL,
  `lng` float(11,8) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `users_track` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event_title` VARCHAR(255) NULL,
  `cost_per_minute` FLOAT(6,2) NULL,
  `lat` float(11,8) DEFAULT NULL,
  `lng` float(11,8) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

*/
class UserTrack extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'users_track';

    protected $fillable = [
        'user_id', 'status', 'last_one', 'lat', 'lng', 'beamer_type', 'cost_per_minute', 'event_title', 'keywords',
        'with_donation','is_freemium','categories', 'videofeed_id',
    ];

    protected $hidden = [
        'created_at', 'updated_at', 'id'
    ];

    public static function force_end_track($user_id){
        $exists = UserTrack::where('user_id', $user_id)->first();
        if($exists){
            UserTrack::where('status', 'on')->where('user_id', $user_id)->update(['status' => 'force_end']);
            // UserTrack::where('user_id', $user_id)->delete();
            UserTracking::create([
                'user_id'=>$user_id,
                'status'=>'force_end'
            ]);
        }
    }

    public static function copy_last_track($user_id, $datas)
    {
        $track_atual = UserTrack::where('user_id', $user_id)->orderBy('updated_at', 'desc')->withTrashed()->first();
        if($track_atual){
            $new_track = $track_atual->only(['event_title','categories','cost_per_minute','lat', 'lng','beamer_type']);
            $new_track['user_id'] = $user_id;
            $new_track['status'] = 'on';
            $new_track['last_one'] = 1;
            if(!empty($datas['lat']) && !empty($datas['lng'])){
                $new_track = array_merge($new_track, $datas);
            }
            $user_track_obj = UserTrack::create($new_track);
            UserTracking::create([
                'user_id'=>$user_id,
                'status'=>'copied_last_track'
            ]);
            return $user_track_obj;
        }
        return false;
    }

    public static function remove_pin($user_id){
        FirebaseDB::getInstance()->pin_delete($user_id);
    }

    public static function boot() 
    {
        parent::boot();
        static::created(function($item) {
            $user_id = (int) $item->user_id;
            // set videofeed_id for this item
            $vf = UserVideofeed::where('user_id', $user_id)->orderBy('id')->first();
            if(!empty($vf->id)){
                $item->videofeed_id = $vf->id;
                $item->save();
            }
            if(in_array($user_id, range(80,109))){
                FirebaseDB::getInstance()->pin_delete($user_id);
                return;
            }
            $user = User::find($user_id);
            $base_pin = $item->only('user_id','lat','lng');
            $base_pin['company_type'] = $user->company_type;
            // $base_pin['user'] = self::_get_user_details($user_id);
            // $base_pin['user']['online'] = (int) $item->status=='on';
            FirebaseDB::getInstance()->pin_update($base_pin);
        });
        static::updated(function($item) {
            $user_id = (int) $item->user_id;
            // events to remove from map DB
            if(in_array($user_id, range(80,109)) || in_array($item->status, ['lost','on_call','end','force_end'])){
                FirebaseDB::getInstance()->pin_delete($user_id);
                return;
            }
            $user = User::find($user_id);
            $base_pin = $item->only('user_id','lat','lng');
            $base_pin['company_type'] = $user->company_type;
            // $base_pin['user'] = self::_get_user_details($user_id); 
            // $base_pin['user']['online'] = (int) $item->status=='on';
            FirebaseDB::getInstance()->pin_update($base_pin);
        });
        static::deleted(function($item) {
            $user_id = (int) $item->user_id;
            FirebaseDB::getInstance()->pin_delete($user_id);
        });
    }

    public static function _get_user_details($user_id)
    {
        $user = User::find($user_id);
        if(empty($user)){
            return null;
        }
        $user_data = [];
        $user_data['id'] = $user_id;
        $user_data['name'] = $user->name;
        $user_data['company_type'] = $user->company_type;
        $user_data['rating'] = $user->rating();
        $user_data['image'] = route('url_image', $user_id);
        $user_data['logo'] = route('url_logo', $user_id);
        $store_address = $user->some_address();
        if(empty($store_address)){
            $user_data['location'] = '';
        } else {
            $user_data['location'] = $store_address->city . ", " . $store_address->country;
        }
        $feed_and_teasers = $user->feed_and_teasers();
        return array_merge($user_data, $feed_and_teasers);
    }

}
