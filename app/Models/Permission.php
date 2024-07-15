<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as PermissionSpartie;

class Permission extends PermissionSpartie
{
    public static function getPermisionToInterface(){
        $perms = Permission::where('name', 'like', '%delete')
                        ->orWhere('name', 'like', '%create')->get();
        $ar = [];
        foreach($perms as $perm){

            $pieces = explode("-", $perm->name);
            if(strpos($pieces[1], 'delete')!==false){
                $name =  $pieces[0] . "_apagar";
            } else if(strpos($pieces[1], 'create')!==false){
                $name =  $pieces[0] . "_verealterar";
            }
            $ar[ $name ] = [
                'id'=>$perm->id,
                'name'=>$name,
                'title'=>$perm->title,
                'actual_value'=>false,
            ];
        }
        return $ar;
    }


}
