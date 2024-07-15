<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class UserVideofeed extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'user_videofeed';

    protected $fillable = [
        'user_id', 'url', 'original', 'converted', 'thumb',
        'original_size', 'converted_size', 'service', 'teaser_text', 'teaser_style', 'command_output','command_input'	
    ];

    protected $hidden = [
        'created_at', 'updated_at', 'user_id', 'id', 'deleted_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categories()
    {
        $sql = "select categories from users_track where videofeed_id = ? order by id desc";
        $categories = \DB::select($sql, [$this->id]);
        if(empty($categories[0]->categories)) {
            $sql = "select categories from users_track where user_id = ? order by id desc";
            $categories = \DB::select($sql, [$this->user_id]);
        }
        if(empty($categories[0]->categories)) {
            return '0';
        }
        return $categories[0]->categories;
    }

}
