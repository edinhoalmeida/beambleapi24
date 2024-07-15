<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    use HasFactory;

    protected $table = 'images_b64';

    protected $fillable = [
        'url', 'thumbnail','title',
        'imageable_id', 'imageable_type',
        'modifier_id'
    ];

    protected $hidden = [
        'created_at', 'updated_at', 'modifier_id', 'imageable_id', 'imageable_type',
    ];


    public function getUrlFullAttribute()
    {
        return Storage::disk('public')->url($this->url);
    }

    public function getUrlThumbnailAttribute()
    {
        return Storage::disk('public')->url($this->thumbnail);
    }

    public function deleteOldImages(){
        Storage::disk('public')->delete($this->url);
        Storage::disk('public')->delete($this->thumbnail);
    }

    public function imageable()
    {
        // return $this->morphTo(__FUNCTION__, 'imageable_type', 'imageable_id');
        return $this->morphTo();
    }
}
