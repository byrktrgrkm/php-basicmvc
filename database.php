<?php

defined('BASEPATH') or die('not found');

class Database
{
    protected $db;

    public function __construct()
    {
        try {
            $this->db = new PDO('mysql:host='.DB_HOST, DB_USERNAME, DB_PASSWORD,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            $this->db->exec("use ".DB_NAME);

        } catch (PDOException $e) {
            echo $e->getMessage();
            exit;
        }
    }
    

}
