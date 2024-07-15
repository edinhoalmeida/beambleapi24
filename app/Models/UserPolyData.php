<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\UserBeamerData;
use App\Models\UserClientData;


class UserPolyData extends Model
{
    use HasFactory;
    
    protected $table = 'users';

    public static function set_client_data($user_id, $data = null)
    {
        $has = UserClientData::where('user_id', $user_id)->first();
        if(empty($data)){
            $data = User::where('id', $user_id)->first()->toArray();
        }
        if ($has){
            $has->update($data);
        } else {
            $data['user_id'] = $user_id;
            $has = UserClientData::create($data);
        }
        // if empty a beamer data make a copy
        $has = UserBeamerData::where('user_id', $user_id)->first();
        if (empty($has)){
            $data['user_id'] = $user_id;
            $has = UserBeamerData::create($data);
        }
    }

    public static function set_beamer_data($user_id, $data = null){
        $has = UserBeamerData::where('user_id', $user_id)->first();
        if(empty( $data)){
            $data = User::where('id', $user_id)->first()->toArray();
        }
        if ($has){
            $has->update($data);
        } else {
            $data['user_id'] = $user_id;
            $has = UserBeamerData::create($data);
        }
        // if empty a client data make a copy
        $has = UserClientData::where('user_id', $user_id)->first();
        if(empty($has)){
            $data['user_id'] = $user_id;
            unset($data['company_type']);
            $has = UserClientData::create($data);
        }
    }

    public static function get_client_data($user_id)
    {
        $has = UserClientData::where('user_id', $user_id)->first();
        
        if(empty($has)){
            return [];
        }
        $has = $has->toArray();

        unset($has['deleted_at']);
        unset($has['created_at']);
        unset($has['updated_at']);
        unset($has['user_id']);
        unset($has['id']);

        return $has;
    }

    public static function get_beamer_data($user_id)
    {
        $has = UserBeamerData::where('user_id', $user_id)->first();
        if(empty($has)){
            return [];
        }
        $has = $has->toArray();
        unset($has['deleted_at']);
        unset($has['created_at']);
        unset($has['updated_at']);
        unset($has['user_id']);
        unset($has['id']);
        return $has;
    }
}
