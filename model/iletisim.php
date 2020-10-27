<?php



class iletisim extends Model{

    public function GetContactStatusList(){
        return $this->from('durumlar')->where('tablo','bizeulasin')->getAll();
    }
    public function ContactCheck($id){
        return $this->select(' tablo ')->from('durumlar')->where("id",$id)->getOne()['tablo'] == 'bizeulasin';
    }
    public function AddContact($params){
        return (bool)$this->from('bizeulasin')->insert2($params);
    }

}