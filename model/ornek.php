<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Ornek extends Model{

    public function KullaniciBilgileriniGetir(){
        return $this->from('kullanicilar')->where([
            "onay"=>1
        ])->getAll();
    }

}