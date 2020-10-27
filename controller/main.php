<?php 


defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends Controller
{

    private $data = [];

    private $userModel;


    public function __construct(){
        parent::__construct();

        $this->helper('cookie');

        $this->view('template/header',$this->data);
    }

    public function index(){

        echo "index";
    }
    public function username($username){
        echo "Merhaba ". $username;
    }

    public function route($v){
        // route paging
        
    }
    
    private function error404Page(){
        $this->view('template/header',$this->data);
        $this->view('404');
    }

    
    public function yardim(){
        $this->statik('yardim');
    }
    public function telifhakki(){
        $this->statik('telif-hakki');
    }
    
    public function cerezpolitikasi(){
        $this->statik('cerez-politikasi');
    }
    public function dogruluk(){
        $this->statik('dogruluk');
    }
    
    public function gizlilikpolitikasi(){
        $this->statik('gizlilik-politikasi');
    }
    public function kotuyekullanim(){
        $this->statik('kotuye-kullanim');
    }
    public function kullanimsartlari(){
        $this->statik('kullanim-sartlari');
    }
    public function misyonumuz(){
        $this->statik('misyonumuz');
    }
    public function vizyonumuz(){
        $this->statik('vizyonumuz');
    }


    public function __destruct(){
        $this->view('template/footer');
    }

}