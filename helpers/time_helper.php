<?php

/**
 * solution source
 * https://stackoverflow.com/a/18602474
 */
date_default_timezone_set('Europe/Istanbul');
function time_elapsed_string($datetime, $full = false) {
    
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'yıl',
        'm' => 'ay',
        'w' => 'hafta',
        'd' => 'gün',
        'h' => 'saat',
        'i' => 'dakika',
        's' => 'saniye',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v;
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' önce' : 'az evvel';
}

function time_normal($time){
    return date("j.n.Y",strtotime($time));
}

function current_time(){
    // 10.05.2020 15:23
    return date("d.m.Y h:i:s",time());
}