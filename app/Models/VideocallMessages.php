<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/*
1	id bigint(20)		UNSIGNED	Não	Nenhum		AUTO_INCREMENT
2	videocall_id	int(11)			Não	Nenhum		
3	user_id	int(11)			Não	Nenhum
4	status	varchar(20)	utf8mb4_unicode_ci		Não	Nenhum		
5	from_text	text	utf8mb4_unicode_ci		Sim	NULL		
6	from_lang	varchar(10)	utf8mb4_unicode_ci		Sim	NULL		
7	target_text	text	utf8mb4_unicode_ci		Sim	NULL		
8	target_lang	varchar(10)	utf8mb4_unicode_ci		Sim	NULL		
9	file_name	varchar(255)	utf8mb4_unicode_ci		Sim	NULL		
10	service_version	varchar(30)	utf8mb4_unicode_ci		Sim	NULL		
11	deleted_at	timestamp			Sim	NULL		
12	created_at	timestamp			Sim	NULL		
13	updated_at	timestamp			Sim	NULL


videocall_id, user_id
status, 
from_text, from_lang, 
target_text, target_lang
file_name
translated_service

*/
class VideocallMessages extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'videocalls_messages';

    protected $fillable = [
        'videocall_id', 'user_id', 'status', 'from_text'
        ,'from_lang', 'target_text', 'target_lang', 'file_name', 'service_version'
    ];

    protected $hidden = [
        'updated_at', 'deleted_at'
    ];

    public function inbox()
    {
        return $this->belongsTo(Videocall::class);
    }
}
