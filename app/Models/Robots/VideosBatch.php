<?php

namespace App\Models\Robots;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/*
CREATE TABLE `video_batch_debug` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `source` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `create_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci
*/

class VideosBatch extends Model
{
    use HasFactory;

    protected $table = 'wv_video_batch_debug';

    protected $fillable = ['source', 'message'];
}
