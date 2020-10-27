<?php


function get_cache_dir(){
    return dirname(__DIR__) . "/cache";
}
function get_cache_path($name){
    return get_cache_dir() . "/" . $name . ".txt"; 
}

function set_cache($name,$data = []){
    $path = get_cache_path($name);

    $file = fopen($path,"w+") or die($path . " not found");
    fwrite($file,is_array($data) ? json_encode($data) : $data);
    fclose($file);
}

function get_cache($name){
    $path = get_cache_path($name);
    if(exist_cache($name)) 
        return file_get_contents($path);
    return "";
}
function exist_cache($name){
    return file_exists(get_cache_path($name));
}
