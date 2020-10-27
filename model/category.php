<?php

defined('BASEPATH') OR exit('No direct script access allowed');
class Category extends Model{

    private $table = "kategoriler";

    public function getCategories(){
        return $this->from($this->table)->getAll();
    }
    public function CheckCategoryId($id,$security = false){
        if($security) return !empty($this->from($this->table)->where('id = :id')->bind2(['id'=>$id])->getOneSecurity());
        return !empty($this->from($this->table)->where('id',$id)->getOne());
    }
    
    public function UserHasCustomizeCategoryId($userid,$kategori_id){
        return !empty($this->from('kullanici_kategori')
        ->where(['user_id'=>$userid,
        'kategori_id'=>$kategori_id])->getOne());
    }
    public function UserAddCustomizeCategoryId($userid,$kategori_id){
        return $this->from('kullanici_kategori')->insert2([
            'kullanici_id'=>$userid,
            'kategori_id'=>$kategori_id
        ]);
    }
    public function UserCustomizeCategoryClear($userid){
        return $this->from('kullanici_kategori')->where('kullanici_id',$userid)->del();
    }
    public function getUserCustomizeCategoryList($userid){
        return $this->from('kullanici_kategori')->where('kullanici_id',$userid)->getAll();
    }
    public function UserCustomizeCategory($userid){

        $sql = "SELECT ka.*,IF(T.total is null or kk.kategori_id is not null,1,0) as customized FROM kategoriler ka 
        LEFT JOIN (SELECT COUNT(*) as total,x.id FROM kullanici_kategori x WHERE x.kullanici_id = {$userid} ) as T ON T.total > 0 
        LEFT JOIN (SELECT * FROM kullanici_kategori k WHERE k.kullanici_id = {$userid}) as kk ON kk.kategori_id = ka.id
        ";

        return $this->set_sql($sql)->getAll();
    }

}