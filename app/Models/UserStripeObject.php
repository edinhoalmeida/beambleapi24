<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/*
tabela grava retorno do stripe:
id Primária bigint(20)      UNSIGNED
user_id int(11)
customer_id varchar(255)
account_id varchar(255)
object_type
object_json (json_encode)
created_at  timestamp           Sim NULL
updated_at  timestamp           Sim NULL
*/
class UserStripeObject extends Model
{

    protected $table = 'user_stripe_objects';

    use HasFactory;

    protected $fillable = [
        'user_id', 'customer_id', 'account_id','object_type', 'object_json'
    ];

    protected $hidden = [
        'created_at', 'id', 'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
