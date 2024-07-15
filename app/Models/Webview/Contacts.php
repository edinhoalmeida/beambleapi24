<?php

namespace App\Models\Webview;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contacts extends Model {
    
    use HasFactory, SoftDeletes;

    protected $table = 'wv_contacts';

    protected $fillable = [
        'profile_type',
        'name',
        'surname',
        'email',
        'message',
        'status'
    ];

    protected $hidden = [
        'deleted_at', 'created_at', 'updated_at'
    ];

}