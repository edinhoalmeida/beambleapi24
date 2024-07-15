<?php

namespace App\Models\Financial;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/*
fin_payments
    id
    user_id_client
    user_id_beamer
    call_id
    is_guarantee
    type
    amount
    amount_products
    fee
    method_type
    status
16  deleted_at  timestamp           Sim NULL
17  created_at  timestamp           Sim NULL
18  updated_at  timestamp           Sim NULL    

*/
class Payment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'fin_payments';
    protected $fillable = [
        'user_id_beamer', 'user_id_client', 'call_id', 'is_guarantee', 'type', 
        'amount', 'amount_products', 'fee', 'method_type', 'status'
    ];
    protected $hidden = [
        'updated_at', 'deleted_at'
    ];

}
