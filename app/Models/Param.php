<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/*
    id Primária bigint(20)      UNSIGNED    Não Nenhum      AUTO_INCREMENT
2   name    varchar(255)    utf8mb4_unicode_ci      Não Nenhum      
3   value   varchar(255)    utf8mb4_unicode_ci      Não Nenhum      
4   modifier_id int(11)         Sim NULL        
5   deleted_at  timestamp           Sim NULL        
6   created_at  timestamp           Sim NULL        
7   updated_at  timestamp           Sim NULL

*/

class Param extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'value', 'modifier_id'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public static $params = null;

    public static function addOrUpdate($objeto, $modifier_id=null){
        foreach($objeto as $name => $value){
            self::addOrUpdateOne($name, $value, $modifier_id);
        }
    }


    public static function addOrUpdateOne($name, $value, $modifier_id =null){
        $saved_to_delete = Param::where('name', $name)->first();
        if( ! empty($saved_to_delete) ){
            $saved_to_delete->modifier_id = $modifier_id;
            $saved_to_delete->save();
            $saved_to_delete->delete();
        }
        $dados = [
            'name'=>$name,
            'value'=>$value,
            'modifier_id'=>$modifier_id
        ];
        Param::create($dados);
    }

    public static function getArray()
    {
        //https://laravel.com/docs/8.x/queries#retrieving-all-rows-from-a-table
        if(!empty(self::$params)){
            return self::$params;
        }
        $all = Param::all();
        $return = [];
        foreach($all as $line){
            $return[ $line->name ] = $line->value;
        }
        self::$params = $return;
        return $return;
    }

    public static function getParam($key)
    {
        //https://laravel.com/docs/8.x/queries#retrieving-all-rows-from-a-table
        if(empty(self::$params)){
            Param::getArray();
        }
        if(!empty( self::$params[ $key ] )){

            return self::$params[ $key ];
            // 
        }
        return null;
    }

    

}
