<?php 




function _setcookie($key,$value){
    setcookie($key, $value, time()+3600,"/"); // 1 saat; 
}

function _setcookietime($key,$value,$time){
    setcookie($key,$value,$time,'/');
}

function _setlocalcookie($key,$value){

    setcookie($key, $value, time()+600,"/"); // 10 dk; 
}

function _setViewCookie($name){
    setcookie($name, 1, time()+120,"/"); // 2 dk; 
}

function _setCustomCookie($key,$val,$type = "folder"){
    if($type == "public"){ /**  */
        setcookie($key,"true",time()+600,"/");
    }else{//domain
        setcookie($key,"true",time()+600);
    }
}

function _hascookie($key){
    return isset($_COOKIE[$key]);
}

function _cookie($key){
    return isset($_COOKIE[$key]) ? $_COOKIE[$key] : "";
}

function _delcookie($key){
    if(!_hascookie($key)) return false;
    setcookie($key, "", time()-3600,"/");
    return true;
}


?>