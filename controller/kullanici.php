<?php 


defined('BASEPATH') OR exit('No direct script access allowed');

class Kullanici extends Controller
{

    private $data = [];

    private $userModel;


    public function __construct(){
        parent::__construct();

        $this->helper('cookie');
    }

    public function liste(){
        $ornekModel = $this->model('ornek');
        $kullanicibilgileri = $ornekModel->KullaniciBilgileriniGetir();

        $this->view("kullanicibilgileri",["bilgiler" => $kullanicibilgileri]);
    }

 

}