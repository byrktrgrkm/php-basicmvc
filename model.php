<?php

defined('BASEPATH') or die('not allowed');

class Model extends Database
{

    
    protected $_tablename;
    protected $_columns = "*";
    protected $_limit = "";
    protected $_where = "";
    protected $_order = "";
    protected $_sql = "";
    protected $_set = "";
    protected $_set_values = [];
    protected $_set_only = false;
    


    public $errorMessage = "";

    public function use($dbname){
        $this->db->query("use ".$dbname);
        return $this;
    }
    public function table($tablename){
        $this->_tablename = $tablename;
        return $this;
    }
    public function from($tablename){
        return $this->table($tablename);
    }
    public function query($sorgu){
        $this->set_sql($sorgu);
        return $this;
    }
    public function col($columns =[]){
        $this->_columns = implode(',',$columns);
        return $this;
    }
    public function select($column){
        $this->_columns = $column;
        return $this;
    }
    public function limit($offsetOrLimit = "",$limit = ""){
        if($offsetOrLimit == ""){
            $this->errorMessage = "Limit eklenemedi!";
            return;
        }
        $this->_limit = "LIMIT ";
        if($limit == "")
            $this->_limit .= $offsetOrLimit;
        else
            $this->_limit .= $offsetOrLimit.",".$limit;

        
        return $this;
    }

    public function search($col,$search){
  
        
        if($this->_where == "")
            $this->_where = "WHERE ";
        else
            $this->_where = " && ";

        $this->_where .= $col." LIKE "."'%".$search."%' ";

        return $this;
            
    }
    
    public function where($kosullar,$value = "",$delimiter = ' && '){
    
        if(is_array($kosullar)){
            $this->_where = "WHERE ";
            foreach($kosullar as $col => $values ){

                if(is_array($values)){
                    if(!isset($values["operation"])){
                        $values["operation"] = "=";
                    }

                    if(is_numeric($values["val"]))
                        $this->_where .=  $col . " ".$values["operation"] . " ".$values["val"]." ";  
                    else
                        $this->_where .=  $col . " ".$values["operation"] . "'".$values["val"]."'";
                }else{
                        $this->_where .= $col . "='".$values."'";
                }   
                    
                if($col !== array_key_last($kosullar)){
                    $this->_where .= ' '.$delimiter.' ';
                }
            }
        }else{
            if(!empty($value)){
                $this->_where = "WHERE ".$kosullar."= '".$value."'";
            }else{
                $this->_where = "WHERE ".$kosullar;
            }
        }       
        return $this;
    }
    public function order($descOrAsc = 1){
        $D_A = $descOrAsc < 0 ? "DESC" : "ASC";
        if($this->_columns == "*" )
            return;
        
        $columns = explode(',',$this->_columns);
        foreach($columns as $key => $column){
            $columns[$key] .= " ".$D_A;
        }

        $this->_order = " ORDER BY ".implode(',',$columns);
        return $this;

            
    }
    public function order2($descOrAsc){

        $this->_order = " ORDER BY ".$descOrAsc;
        return $this;

            
    }
    public function getAll(){
        if(!$this->isSelectedTable())
            return;
        $data = $this->db->query($this->sql())->fetchAll(PDO::FETCH_ASSOC);
        $this->begin();
        return $data;
    }

    public function getOne(){
        if(!$this->isSelectedTable())
            return;
        $data = $this->db->query($this->sql())->fetch(PDO::FETCH_ASSOC);
        $this->begin();
        return $data;
    }

    
    public function count(){
        if(!$this->isSelectedTable())
            return;
        $this->select(' COUNT(*) as total ');
        return $this->getOne()['total'];
    }

    public function getAllSecurity(){
        if(!$this->isSelectedTable())
            return;   


        $prepare = $this->db->prepare($this->sql());

        if(!empty($this->_set_values)) $prepare->execute($this->_set_values);
        else $prepare->execute(); 

        //echo $prepare->debugDumpParams();

        $data = $prepare->fetchAll(PDO::FETCH_ASSOC);
        
        $this->begin();
        return $data;
    }
    public function getOneSecurity(){
        if(!$this->isSelectedTable())
            return;   
        
       
        $prepare = $this->db->prepare($this->sql());
        if(!empty($this->_set_values)) $prepare->execute($this->_set_values);
        else $prepare->execute();
        $data = $prepare->fetch(PDO::FETCH_ASSOC);
  
        $this->begin();
        return $data;
    }

    public function bind2($array = []){
        $this->_set_values = $array;
        return $this;
    }

    public function isSelectedTable(){
        if(empty($this->_sql) && $this->_tablename == null){
            echo "not selected a table";
        }
        return isset($this->_tablename) || !empty($this->_sql);
    }
    public function set_sql($set){
        $this->_sql = $set;
        return $this;
    }
    public function sql(){
        if(!empty($this->_sql))
            return $this->_sql;
        if(!empty($this->_set))
            return "[ insert|update ] ".$this->_set." ".$this->_where;
        $sql = "Select ".$this->_columns." from ".$this->_tablename." ".$this->_where." ".$this->_order." ".$this->_limit;
        return $sql;
    }
    public function begin(){
        $this->_tablename = "";
        $this->_columns = "*";
        $this->_limit = "";
        $this->_where = "";
        $this->_sql = "";
        $this->_order = "";
        $this->_set = "";
        $this->_set_values = [];
        $this->_set_only = false;
    }
   
    public function update($sql,$array){
        $p = $this->db->prepare($sql);
        $r = $p->execute($array);
        
        return $r ? 1 : 0;
    }

    public function update2($security = true){
        if(empty($this->_tablename)) return false;
        if(empty($this->_where) && $security){
            $this->errorMessage = "WHERE koşulunda parametre bulunmadığından güncelleme işlemi kısıtlandı.";
            return false;
        }
        $sql = "UPDATE ".$this->_tablename." ".$this->_set." ".$this->_where;
        $pre = $this->prepare($sql);
        if($this->_set_only)
            $exe = $pre->execute();
        else $exe = $pre->execute($this->_set_values);

        $this->begin();
        if(!$exe) $this->errorMessage =$pre->errorInfo()[2];
        return (bool)$exe;
    }
    public function prepare($sql){
        return $this->db->prepare($sql);
    }
    public function set($arr,$orValue=""){
        $this->_set = " SET ";

        if(is_array($arr)){

            foreach($arr as $col => $value){
                $this->_set .= $col." = :".$col."";
                if(array_key_last($arr) != $col)
                    $this->_set .=", ";
            }
            $this->_set_values = $arr;
        }else{
            if(empty($orValue)){
                $this->_set .= $arr;
                $this->_set_only = true;
            }else{
                $this->_set .= $arr."= :".$arr;
                $this->_set_values = [$arr=>$orValue];
            }
        }
        return $this;
    }

    
    
    // UPDATE tablo_adi SET col = x WHERE id = 2

   
    public function insert_manuel($sql,$array){
        $p = $this->db->prepare($sql);
        
        $r = $p->execute($array);
            
        if(!$r){
            $errorMessage = $p->errorInfo()[2];
            return false;
        }
           
        return true;
       
    }

    public function insert2($arr = []){
        if(!is_array($arr)) return false;
        else if(count($arr) > 0){
            $sql = "INSERT INTO ".$this->_tablename." SET ";

            foreach($arr as $column => $value){
                $sql .= $column." = :".$column;
                if($column != array_key_last($arr))
                    $sql .= ", ";
            }

            $prepare = $this->db->prepare($sql);
            $result = $prepare->execute($arr);
             if(!$result){
                $this->errorMessage = $prepare->errorInfo()[2];
             }
    
            return (bool)$result;        
        }

        return false;
    }

    public function insert(){

        if(empty($this->_tablename)) return false;
        $sql = "INSERT INTO ".$this->_tablename." ".$this->_set;
        $pre = $this->prepare($sql);
        if($this->_set_only)
            $exe = $pre->execute();
        else $exe = $pre->execute($this->_set_values);
        $this->begin();
        if(!$exe) $this->errorMessage =$pre->errorInfo()[2];
        return (bool)$exe;

    }

    public function del($secure = true){
        if(empty($this->_tablename)) return false;
        if($secure && empty($this->_where)){
            $this->errorMessage = "WHERE koşulunda parametre bulunmadığından silme işlemi kısıtlandı.";
            return false;
        }
        $sql = "DELETE FROM ".$this->_tablename." ".$this->_where." ".$this->_limit;
        $pre = $this->prepare($sql);
        $exe = $pre->execute();
        $this->begin();
        if(!$exe) $this->errorMessage =$pre->errorInfo()[2];
        return (bool)$exe;
        
    }

   

    public function returnJSON($type,$val){
        $return[$type] = $val;
        echo json_encode($return);
        die;
    }

    public function GetLastInsertId(){
        return $this->db->lastInsertId();
    }

    public function ErrorMessage(){
        return $this->errorMessage;
    }

    public function __desctruct(){
        $this->db = null;
    }



}