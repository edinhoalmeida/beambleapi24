<?php

namespace App\Models\Webview;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
/*
CREATE TABLE `wv_faqs` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `body_txt` text NOT NULL,
  `lang_code` char(8) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `wv_faqs`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `wv_faqs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

*/
class Faqs extends Model {
    
    use HasFactory, SoftDeletes;

    protected $table = 'wv_faqs';

    protected $fillable = [
        'title',
        'body_txt',
        'lang_code'
    ];

    protected $hidden = [
        'deleted_at', 'created_at', 'updated_at'
    ];

}