<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/*
id Primária bigint(20)      UNSIGNED
2   user_id int(11)
3   lang_code varchar(100)
16  deleted_at  timestamp           Sim NULL
17  created_at  timestamp           Sim NULL
18  updated_at  timestamp           Sim NULL
*/
class UserLang extends Model
{

    protected $table = 'user_lang';

    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'user_id', 'lang_code'
    ];

    protected $hidden = [
        'created_at', 'updated_at', 'user_id', 'deleted_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
