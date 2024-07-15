<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/*
id Primária bigint(20)      UNSIGNED
2   user_id int(11)
3   address varchar(255)    utf8mb4_unicode_ci      Não Nenhum
4   city    varchar(255)    utf8mb4_unicode_ci      Sim NULL
5   country varchar(255)    utf8mb4_unicode_ci      Sim NULL
6   country_code    varchar(20) utf8mb4_unicode_ci      Sim NULL
7   street  varchar(255)    utf8mb4_unicode_ci      Sim NULL
8   street2 varchar(255)    utf8mb4_unicode_ci      Sim NULL
9   street_number   varchar(255)    utf8mb4_unicode_ci      Sim NULL
10  postal_code varchar(255)    utf8mb4_unicode_ci      Sim NULL
11  others  varchar(255)    utf8mb4_unicode_ci      Sim NULL
12  others_key  varchar(255)    utf8mb4_unicode_ci      Sim NULL
13  address_type
14  lat float(11,8)         Sim NULL
15  lng float(11,8)         Sim NULL
16  deleted_at  timestamp           Sim NULL
17  created_at  timestamp           Sim NULL
18  updated_at  timestamp           Sim NULL
*/
class UserAddresses extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'user_id', 'address', 'address_type', 'city', 
        'country', 'country_code', 'street', 'street2',
        'street_number', 'postal_code', 'others',
        'others_key', 'lat', 'lng','raw_off_set'
    ];

    protected $hidden = [
        'created_at', 'updated_at', 'user_id', 'id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function getAddressToBill($user_id){
        $address = UserAddresses::where('user_id', $user_id)
                ->orderByRaw("
                    CASE address_type 
                        WHEN 'billing' THEN 1 
                        WHEN 'contact' THEN 2
                        WHEN 'shipping' THEN 3
                        WHEN 'store' THEN 4
                        ELSE 5 END 
                    ASC
                ")->first();
        $dados = [];
        if(!empty($address->country_code)){
            $dados['country'] = $address->country_code;
        }
        if(!empty($address->city)){
            $dados['city'] = $address->city;
        }
        if(!empty($address->postal_code)){
            $dados['postal_code'] = $address->postal_code;
        }
        if(!empty($address->street)){
            $dados['line1'] = $address->line1;
            if(!empty($address->street_number)){
                $dados['line1'] .= ', ' . $address->street_number;
            }
        }
        if(!empty($address->others_key)){
            $akeys = explode(', ', $address->others_key);
            $avalues = explode(', ', $address->others);
            if(!empty($akeys[0]) && $akeys[0]=='administrative_area_level_1'){
                $dados['state'] = $avalues[0];
            }
        }
        return $dados;
    }

    public function get_shipping(){
        $address = UserAddresses::where('user_id', $this->user_id)
                ->orderByRaw("
                    CASE address_type
                        WHEN 'shipping' THEN 1 
                        WHEN 'contact' THEN 2
                        WHEN 'billing' THEN 3
                        WHEN 'store' THEN 4
                        ELSE 5 END 
                    ASC
                ")->first();
        $dados = [];
        $dados['address'] = $address->address;
        $dados['postal_code'] = $address->postal_code;
        $dados['city'] = $address->city;
        // if(!empty($address->others_key)){
        //     $akeys = explode(', ', $address->others_key);
        //     $avalues = explode(', ', $address->others);
        //     if(!empty($akeys[0]) && $akeys[0]=='administrative_area_level_1'){
        //         $dados['state'] = $avalues[0];
        //     }
        // }
        $dados['country'] = $address->country_code;

        return $dados;
    }
}
