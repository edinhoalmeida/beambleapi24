<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserClientData extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = 'users_client_data';

    protected $fillable = [
        'user_id', 'name', 'surname', 'phone', 'company_name', 'company_doc', 'company_type', 'company_description','second_hand_resaler','level_expertise', 'accept_parcel_return'
    ];
}
