<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\ImageB64;

class VideocallProductsOld extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'videocalls_products';

    protected $fillable = [
        'videocall_id', 'status', 'title', 'product_price','product_currency',
        'description', 'client_accepted_at'
    ];

    protected $hidden = [
        'updated_at', 'deleted_at'
    ];

    public function inbox()
    {
        return $this->belongsTo(Videocall::class);
    }

    public function image()
    {
        return $this->morphOne(ImageB64::class, 'imageable');
    }

}
