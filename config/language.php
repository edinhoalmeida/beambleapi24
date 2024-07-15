<?php 

$base_url = env('APP_URL') . '/icons/flags/';

// count 142
$languages_list = [
    ["name"=>"Arabic","code"=>"ar","flag"=>$base_url."arabic.svg"],
    ["name"=>"Chinese","code"=>"zh","flag"=>$base_url."cn.svg"],
    ["name"=>"Czech","code"=>"cs","flag"=>$base_url."cz.svg"],
    ["name"=>"Danish","code"=>"da","flag"=>$base_url."dk.svg"],
    ["name"=>"Dutch","code"=>"nl","flag"=>$base_url."nl.svg"],
    ["name"=>"English","code"=>"en","flag"=>$base_url."gb-eng.svg"],
    ["name"=>"Finnish","code"=>"fi","flag"=>$base_url."fi.svg"],
    ["name"=>"French","code"=>"fr","flag"=>$base_url."fr.svg"],
    ["name"=>"German","code"=>"de","flag"=>$base_url."de.svg"],
    ["name"=>"Greek","code"=>"el","flag"=>$base_url."gr.svg"],
    ["name"=>"Hebrew","code"=>"he","flag"=>$base_url."il.svg"],
    ["name"=>"Hindi","code"=>"hi","flag"=>$base_url."in.svg"],
    ["name"=>"Hungarian","code"=>"hu","flag"=>$base_url."hu.svg"],
    ["name"=>"Indonesian","code"=>"id","flag"=>$base_url."id.svg"],
    ["name"=>"Italian","code"=>"it","flag"=>$base_url."it.svg"],
    ["name"=>"Japanese","code"=>"ja","flag"=>$base_url."jp.svg"],
    ["name"=>"Korean","code"=>"ko","flag"=>$base_url."kp.svg"],
    ["name"=>"Norwegian","code"=>"no","flag"=>$base_url."no.svg"],
    ["name"=>"Polish","code"=>"pl","flag"=>$base_url."pl.svg"],
    ["name"=>"Portuguese","code"=>"pt","flag"=>$base_url."pt.svg"],
    ["name"=>"Russian","code"=>"ru","flag"=>$base_url."ru.svg"],
    ["name"=>"Spanish","code"=>"es","flag"=>$base_url."es.svg"],
    ["name"=>"Swedish","code"=>"sv","flag"=>$base_url."se.svg"],
    ["name"=>"Thai","code"=>"th","flag"=>$base_url."th.svg"],
    ["name"=>"Turkish","code"=>"tr","flag"=>$base_url."tr.svg"],
    ["name"=>"Ukrainian","code"=>"uk","flag"=>$base_url."ua.svg"],
    ["name"=>"Vietnamese","code"=>"vi","flag"=>$base_url."vn.svg"]
];

$languages_to_form = [];
foreach($languages_list as $ll){
    $languages_to_form[ $ll['code'] ] = $ll['name'];
}
$languages_to_json = [];
foreach($languages_list as $ll){
    $languages_to_json[] = (object) $ll;
}
return [
    'languages_to_form'=>$languages_to_form,
    'languages_to_json'=>$languages_to_json,
];