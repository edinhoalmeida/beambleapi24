<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Storage;

class ImageB64 extends Model
{
    use HasFactory;

    protected $table = 'images_b64';

    protected $fillable = [
        'base64', 'thumbnail',
        'imageable_id', 'imageable_type','disk_path',
        'modifier_id', 'type'
    ];

    protected $hidden = [
        'created_at', 'updated_at', 'modifier_id', 'imageable_id', 'imageable_type',
    ];

    public function imageable()
    {
        // return $this->morphTo(__FUNCTION__, 'imageable_type', 'imageable_id');
        return $this->morphTo();
    }
}
