<?php

(defined('BASEPATH') || defined('FILEMANAGER') ) or die('not found');


const PRODUCT = 'PRODUCT',DEV = 'DEV';


// set mode dev or product

const SERVER_MODE = DEV;

// set version css,js updates
define('VERSION',"1.0");

if(SERVER_MODE ==  "PRODUCT"){

    define('BASE_URL','https://websiteadresi.com');

    define('FOLDER_NAME','');
    
    define('DB_HOST','localhost');
    define('DB_NAME','');
    define('DB_USERNAME','');
    define('DB_PASSWORD','');

}else if(SERVER_MODE == "DEV"){

    define('BASE_URL','http://localhost/basicmvc');

    define('FOLDER_NAME','basicmvc');

    define('DB_HOST','localhost');
    define('DB_NAME','basicmvc');
    define('DB_USERNAME','root');
    define('DB_PASSWORD','');

}


define('YONETIM_FOLDER','yonetimpaneli');

define('AJAX_URL',BASE_URL.'/ajax');


define('NOTIFICATION_CHECK_MS',30 * 1000);

// DEFINE PRODUCT 

define('PAGE_TITLE','');
define('PAGE_KEYWORDS','');
define('PAGE_DESCRIPTION','');


