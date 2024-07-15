<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\ImageB64;

/*
DROP TABLE IF EXISTS `user_user_products`;
CREATE TABLE `user_user_products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `videocall_id` int NOT NULL,
  `client_id` int NOT NULL,
  `beamer_id` int NOT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_accepted_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
*/
class UserUserProducts extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'user_user_products';

    protected $fillable = [
        'product_id', 'videocall_id', 'client_id', 'beamer_id', 'status', 'client_accepted_at'
    ];

    protected $hidden = [
        'updated_at', 'deleted_at'
    ];

    public function inbox()
    {
        return $this->belongsTo(Videocall::class);
    }

    public function product()
    {
        return $this->belongsTo(UserProducts::class, 'product_id');
    }

}
