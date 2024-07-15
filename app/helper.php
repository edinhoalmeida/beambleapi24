<?php
/**
 *  Constants globals
 */
if(!empty($_GET['dev']) && $_GET['dev']=='1'){
    define('EXCLUDE_USERS', []);
} else {
    $exclude = range(0,519);
    array_filter($exclude, fn($e) => $e !== 385);
    $exclude[] = 522;
    $exclude[] = 523;

    $exclude[] = 561; //rafael
    $exclude[] = 540; //rafael
    $exclude[] = 557; //rafael
    $exclude[] = 529; //rafael
    $exclude[] = 526; //rafael
    
    // anderson 544,547, 562, 
    // rapha 525, 

        // robos liberados
        $robos_ids = range(80,109);
        $exclude = array_filter($exclude, function ($value) use ($robos_ids) {
            return ! in_array($value, $robos_ids);
        });
    define('EXCLUDE_USERS', $exclude);
}
/**
 * Helpers
 */
function permissoes_interface2db($permissoes)
{
    $ar_permissoes = [];
    foreach($permissoes as $permission=>$habilita){
        $pieces = explode("_", $permission);

        $habilita = (bool) $habilita;

        if( ! $habilita) {
            continue;
        }

        if( strpos($pieces[1], 'apagar')!==false ){
            $ar_permissoes[] = $pieces[0] . "-delete";
        }
        if( strpos($pieces[1], 'verealterar')!==false ){
            $ar_permissoes[] = $pieces[0] . "-list";
            $ar_permissoes[] = $pieces[0] . "-create";
            $ar_permissoes[] = $pieces[0] . "-edit";
        }
    }
    return $ar_permissoes;
}

function permissoes_db2interface($permissoes, $default = false)
{
    $ar_permissoes = [];
    foreach($permissoes as $permission){
        $pieces = explode("-", $permission->name);
        if(strpos($pieces[1], 'delete')!==false){
            $ar_permissoes[ $pieces[0] . "_apagar"] = $default;
        }
        if(strpos($pieces[1], 'edit')!==false || strpos($pieces[1], 'create')!==false){
            $ar_permissoes[ $pieces[0] . "_verealterar"] = $default;
        }
    }
    return $ar_permissoes;
}

function permissoes_db2interface_objeto($roles_2_interface, $permissoes)
{
    $ar_permissoes = [];
    foreach($permissoes as $permission){
        $pieces = explode("-", $permission->name);
        if(strpos($pieces[1], 'delete')!==false){
            $name = $pieces[0] . "_apagar";
            $roles_2_interface[ $name ]['actual_value'] = true;
        }
        if(strpos($pieces[1], 'edit')!==false || strpos($pieces[1], 'create')!==false){
            $name = $pieces[0] . "_verealterar";
            $roles_2_interface[ $name ]['actual_value'] = true;
        }
    }
    return array_values($roles_2_interface);
}

function permissoes_interface2db_objeto($interface_2_roles)
{
    $ar_permissoes = [];
    foreach($interface_2_roles as $permission){

        if(!isset($permission['actual_value'])){
            continue;
        }
        if(!is_bool($permission['actual_value'])){
            if($permission['actual_value']=="true"){
                $permission['actual_value'] = true;
            } elseif($permission['actual_value']=="false"){
                $permission['actual_value'] = false;
            } else {
                $permission['actual_value'] = (bool) $permission['actual_value'];
            }
        }

        if( $permission['actual_value'] ){
            $ar_permissoes[ $permission['name'] ] = true;
        } else if( ! $permission['actual_value'] ){
            $ar_permissoes[ $permission['name'] ] = false;
        }
    }
    return permissoes_interface2db($ar_permissoes);
}

function permissoes_user2interface($permissoes)
{
    $ar_permissoes = [];
    foreach($permissoes as $permission){
        $pieces = explode("-", $permission);
        if(strpos($pieces[1], 'delete')!==false){
            $ar_permissoes[ $pieces[0] . "_apagar"] = true;
        }
        if(strpos($pieces[1], 'edit')!==false || strpos($pieces[1], 'create')!==false){
            $ar_permissoes[ $pieces[0] . "_verealterar"] = true;
        }
    }
    return $ar_permissoes;
}

function old_or_db($key, $fromdb = [])
{
    $v = old($key);
    if(!empty($v)) return $v;
    if(!empty($fromdb[$key])) return $fromdb[$key];
    return null;
}

function string_to_number($str, $to_stripe = false)
{
    $str = (string) $str;
    if( strpos($str, ',') ){
        $pieces = explode(',', $str);
    } else if( strpos($str, '.') ){
        $pieces = explode('.', $str);
    } else {
        $pieces = [$str,'00'];
    }
    array_map('trim', $pieces);
    if(strlen($pieces[1])==1){
        $pieces[1] = "0" . $pieces[1];
    } else {
        $pieces[1] = substr($pieces[1], 0, 2);
    }
    if ($to_stripe) {
        $n = implode('', $pieces);
        return (integer) $n;
    }
    $n = implode('.', $pieces);
    $float_value = floatval( $string );
    return $float_value;
}

function generateRandomString($length = 14)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomString;
}

function class_if_error($errors, $key)
{
    if($errors->has($key)){
        return 'is-invalid';
    }
    return ' ';
}

function route_get_class($str)
{
    $rota = request()->route()->getName();
    return $rota == $str ? 'active' : null;
    //return strpos(request()->route()->getName(),$str)===false ? null : 'active';
}

function pr($var)
{
    echo "<pro>";
        if(empty($var)){
            var_dump($var);
        } else {
            print_r($var);
        }
    echo "</pro>";
}

function dblog($source, $text)
{
    DB::table('logs_debug')->insert([
        'source'=>$source,
        'message'=>$text
    ]);
}

function someone_is_empty()
{
    foreach(func_get_args() as $arg){
        if(empty($arg)){
            return true;
        }
    }
    return false;
}