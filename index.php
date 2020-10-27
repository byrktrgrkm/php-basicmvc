<?php


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// accesible
define("BASEPATH",TRUE);


//ob_start();
session_start();

require __DIR__ . '/config.php';
require __DIR__ . '/controller.php';
require __DIR__ . '/database.php';
require __DIR__ . '/model.php';
require __DIR__ . '/route.php';


Route::run('/', "main@index","get");
Route::run('/user/{username}',"main@username","get|post");
Route::run('/kullanicibilgileri',"kullanici@liste","get");
Route::run('/{url}',"main@route","get");

Route::default('404');

