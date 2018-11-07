<?php

//打印函数
function dd($data){
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}

function response($data){
    return json_encode($data);
}

function create_uuid($prefix = ""){
    $str = md5(uniqid(mt_rand(), true));
    $uuid  = substr($str,0,8) . '-';
    $uuid .= substr($str,8,4) . '-';
    $uuid .= substr($str,12,4) . '-';
    $uuid .= substr($str,16,4) . '-';
    $uuid .= substr($str,20,12);
    return $prefix . $uuid;
}