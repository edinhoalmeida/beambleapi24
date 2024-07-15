<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/*
id Primária bigint(20)      UNSIGNED
2   user_id int(11)
3   customer_id varchar(255)
3   customer_stripe_enabled int(1)
16  deleted_at  timestamp           Sim NULL
17  created_at  timestamp           Sim NULL
18  updated_at  timestamp           Sim NULL
*/
class UserStripeCustomer extends Model
{

    protected $table = 'user_stripe_customer';

    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'user_id', 'customer_id', 'customer_stripe_enabled'
    ];

    protected $hidden = [
        'created_at', 'updated_at', 'user_id', 'deleted_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
