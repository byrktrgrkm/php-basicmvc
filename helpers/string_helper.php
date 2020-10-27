<?php 



function RandomString($len = 6)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randstring = '';
        for ($i = 0; $i < $len; $i++) {
            $randstring .= $characters[rand(0, strlen($characters)-1)];
        }
        return $randstring;
    }

function randomMd5($len = 6){
    return substr(md5(RandomString(10)),0,$len);
}

function randomMd52(){
    return md5(rand(1,1 << 10).time());
}

function CustomPassword($str){
    return sha1(md5($str));
}

function rand_id($len = 7){
    $characters = '0123456789';
    $randstring = '';
    for ($i = 0; $i < $len; $i++) {
        $randstring .= $characters[rand(0, strlen($characters)-1)];
    }
    return $randstring;
}

function string_security($str){
    return strip_tags($str);
}

function get_best_repeat_words($word,$count){
    $word = strip_tags($word);
    $list = [];
    $words = explode(' ',$word);
    foreach($words as $w){
        if(strlen($w) >= 3){
            $w = strtolower($w);
            if(isset($list[$w])){
                $list[$w] = $list[$w] + 1;
            }else{
                $list[$w] = 1;
            }
        }
    }
    arsort($list);
    if($count > count($list)){
        $count = count($list);
    }
    $values = array_keys($list);
    $r = [];
    for($i = 0 ; $i< $count;$i++){
        $r[] = $values[$i];
    }
    return $r;
}