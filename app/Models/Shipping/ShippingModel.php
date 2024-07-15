<?php

namespace App\Models\Shipping;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/*
DROP TABLE IF EXISTS `user_shipping`;
CREATE TABLE `user_shipping` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `beamer_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `service` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `uber_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `uber_fee` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `object_json` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


*/
class ShippingModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'user_shipping';
    protected $fillable = [
        'user_id', 'beamer_id', 'service', 'uber_id', 'uber_fee', 'object_json'
    ];
    protected $hidden = [
        'updated_at', 'deleted_at'
    ];

}
