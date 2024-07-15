<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'categories';

    protected $fillable = [
        'parent_id', 'position', 'title', 'icon_url', 'ionicons_class'
    ];

    protected $hidden = [
        'created_at', 'deleted_at'
    ];

    protected static $cache_cats = [];

    public static function get_all()
    {
        $sql = "
        SELECT 
            t1.id, 
            t1.title,
            t1.ionicons_class as name
        FROM categories t1
        ORDER BY 
            t1.position
        ";
        return \DB::select($sql);
    }

    public static function get_details($ids)
    {
        if(!is_array($ids)){
            $ids = [$ids];
        }
        sort($ids);
        $cats_str = implode(',', $ids);
        $md5i = md5($cats_str);
        if(isset(self::$cache_cats[ $md5i ])){
            return self::$cache_cats[ $md5i ];
        }
        $sql = "
        SELECT 
            t1.id, 
            t1.title,
            t1.ionicons_class
        FROM categories t1
            where t1.id IN (". $cats_str .")
        ORDER BY 
            t1.position
        ";
        $result = \DB::select($sql);
        self::$cache_cats[ $md5i ] = $result;
        return $result;
    }

    public static function old_get_all(){
        $base_url = asset('icons');
        $sql = "
        SELECT 
            t1.id, 
            t1.parent_id, 
            t1.title,
            CONCAT('" . $base_url . "', '/', t1.icon_url) as icon_url,
            null as subcategories
        FROM categories t1
        LEFT JOIN categories t2 ON t2.id=t1.parent_id
        ORDER BY 
            COALESCE(t2.position, t1.position), 
            t1.parent_id IS NOT NULL, 
            t1.title
        ";
        $categories = \DB::select($sql);
        $categories = collect($categories);
        $new_cats_db =  $categories->filter(function ($value, $key) {
            return empty($value->parent_id);
        });
        $new_cats = [];
        foreach($new_cats_db as $catc){
            $catc->subcategories=[];
            $new_cats[ $catc->id ] = $catc;
        } 
        $sub_cats =  $categories->filter(function ($value, $key) {
            return !empty($value->parent_id);
        });
        foreach($sub_cats as $subc){
            unset($subc->subcategories);
            unset($subc->icon_url);
            $new_cats[$subc->parent_id]->subcategories[] = $subc;
        } 
        return array_values($new_cats);
    }
}
