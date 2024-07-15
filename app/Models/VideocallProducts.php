<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\ImageB64;
use App\Models\UserUserProducts;

use App\Libs\FirebaseDB;
use App\Http\Resources\VideocallProduct as VideocallProductResource;

/*
DROP TABLE IF EXISTS `videocalls_products`;
CREATE TABLE `videocalls_products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `videocall_id` int NOT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_accepted_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
*/
class VideocallProducts extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'videocalls_products';

    protected $fillable = [
        'product_id', 'videocall_id', 'status', 'client_accepted_at'
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

    public static function boot() 
    {
        parent::boot();
        static::updated(function($item) {
            $videocall_id = (int) $item->videocall_id;
            $product_id = (int) $item->product_id;

            $UserUserProducts = UserUserProducts::where('videocall_id', $videocall_id)
                                    ->where('product_id', $product_id)->first();

            if($UserUserProducts){
                $UserUserProducts->status = $item->status;
                $UserUserProducts->client_accepted_at = $item->client_accepted_at;
                $UserUserProducts->save();
            }
            return $item;
        });
    }

    public static function persist_firebase_db($call_id)
    {
        $products = VideocallProducts::where('videocall_id', $call_id)->get();
        $products = VideocallProductResource::collection($products);
        $persist = [
            'videocall_id' => $call_id,
            'products' => $products
        ];
        try {
            FirebaseDB::getInstance()->videocall_update($persist);
        } catch (\Exception $e) {
            dblog('persist_firebase_db', $e->getMessage());
            return false;
        }
    }


}
