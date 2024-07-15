<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\ImageB64;

class UserProducts extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'user_products';

    protected $fillable = [
        'user_id', 'title', 'product_price','product_currency','product_size','product_weight',
        'description','brand_name','color','fabric','condition','size'
    ];
    
    protected $hidden = [
        'updated_at', 'deleted_at'
    ];

    public function inbox()
    {
        return $this->belongsTo(User::class);
    }

    public function calls()
    {
        return $this->hasMany(VideocallProducts::class);
    }

    public function image()
    {
        return $this->morphOne(ImageB64::class, 'imageable');
    }

}
