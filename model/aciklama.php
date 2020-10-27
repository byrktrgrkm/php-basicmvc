<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Aciklama extends Model{

    private $table = "aciklamalar";

    
    public function AddExplantion($params){
        return (bool) $this->from($this->table)->insert2($params);
    }

    public function AddCron($params){
        return (bool) $this->from("islem")->insert2($params);
    }

    public function GetComments($aciklama_id,$kullanici_id){

   
        $sql = "SELECT y.id,y.kullanici_id,y.icerik,y.tarih,k.kullanici_adi,y.ust_id,CONCAT(k.ad,' ',k.soyad) as 'adsoyad',
        IF(k.uzak_avatar != '',k.uzak_avatar,k.avatar) as avatar,
        IF( y.kullanici_id = {$kullanici_id} or k1.durum = 9 or k1.durum = 7 or k1.durum = 6 ,1,0) as duzenlenebilir,
        IFNULL(yo.begenisayisi,0) as begenisayisi,
		IFNULL(yo.begenmemesayisi,0) as begenmemesayisi,
        IFNULL(yo.begendim,0) as begendim,
        IFNULL(yo.begenmedim,0) as begenmedim
        FROM yorumlar y 
        LEFT JOIN kullanicilar k ON y.kullanici_id = k.id 
        LEFT JOIN kullanicilar k1 ON k1.id = {$kullanici_id}
        LEFT JOIN (
            SELECT 
            	COUNT(IF(yo.durum = 1,1,null)) as begenisayisi,
            	COUNT(IF(yo.durum = 0,1,null)) as begenmemesayisi,
                IF(yo.kullanici_id = {$kullanici_id} and yo.durum = 1 ,1,0) as begendim,
            	IF(yo.kullanici_id = {$kullanici_id} and yo.durum = 0 ,1,0) as begenmedim,
				yo.yorum_id
            FROM yorum_oylar yo GROUP BY yo.yorum_id,yo.durum )  yo
		ON yo.yorum_id = y.id
 
        WHERE y.aciklama_id = {$aciklama_id} and y.durum = 1
        ORDER BY y.tarih DESC";
        return $this->set_sql($sql)->getAll();
    }
    public function GetExplantions($userid){
        return $this->set_sql($this->where_sql("","WHERE ac.durum = 1",$userid))->getAll();
    }
    public function GetExplantionsScroll($userid,$start,$limit){
      
        return $this->set_sql($this->where_sql("","WHERE ac.durum = 1",$userid,$start,$limit))->getAll();

    }
    public function GetExplantionsWithQueryParams($query,$userid){

        $x = "%".trim($query)."%";
        $sql = "WHERE ac.durum = 1 and (ac.baslik like :bul or ac.icerik like :bul )"; 
        return $this->set_sql($this->where_sql("",$sql,$userid))->bind2(["bul"=>$x])->getAllSecurity();

    }
    public function GetExplain($slug,$userid){
        return $this->set_sql($this->where_sql(" ac.icerik "," WHERE ac.durum = 1 and ac.sef_link = '".$slug."'",$userid))->getOne();
    }

    public function CheckLikedExplain($aid,$uid){
        return !empty($this->select(' id ')->from('begeniler')->where(['aciklama_id'=>$aid,'kullanici_id'=>$uid])->getOne());
    }
    public function LikeExplain($aid,$uid){
        return (bool)$this->from('begeniler')->insert2(["aciklama_id"=>$aid,"kullanici_id"=>$uid]);
    }
    public function RevokeExplain($aid,$uid){
        return (bool)$this->from('begeniler')->limit(1)->where(["aciklama_id"=>$aid,"kullanici_id"=>$uid])->del();
    }
    public function GetExplainLikeCount($aid){
        return $this->select(' COUNT(id) as toplam ')->from('begeniler')->where('aciklama_id',$aid)->getOne()['toplam'];
    }

    public function CheckBookmarkExplain($aid,$uid){
        return !empty($this->select(' id ')->from('kaydetmeler')->where(['aciklama_id'=>$aid,'kullanici_id'=>$uid])->getOne());
    }
    public function BookmarkExplain($aid,$uid){
        return (bool)$this->from('kaydetmeler')->insert2(["aciklama_id"=>$aid,"kullanici_id"=>$uid]);
    }
    public function RevokeBookmarkExplain($aid,$uid){
        return (bool)$this->from('kaydetmeler')->limit(1)->where(["aciklama_id"=>$aid,"kullanici_id"=>$uid])->del();
    }
    public function GetExplainBookmarkCount($aid){
        return $this->select(' COUNT(id) as toplam ')->from('kaydetmeler')->where('aciklama_id',$aid)->getOne()['toplam'];
    }

    public function CheckExplain($slug){
        return !empty($this->select(' id ')->from($this->table)->where(['sef_link'=>$slug,'durum'=>'1'])->getOne());
    }
    public function CheckExplainID($aid){
        return ($this->select(' id,kategori_id,kullanici_id,sef_link ')->from($this->table)->where(["id"=>$aid,"durum"=>1])->getOne());
    }
    public function CheckExplainWithID($aid,$kid){
        return ($this->select(' id,kategori_id,kullanici_id,sef_link ')->from($this->table)->where(["id"=>$aid,"kategori_id"=>$kid,"durum"=>1])->getOne());
    }
    public function CheckCategory($category_name){
        return !empty($this->select(' id ')->from("kategoriler")->where('kategori_sef',$category_name)->getOne());
    }
    public function GetRandomExplain(){
        return $this->set_sql("SELECT sef_link FROM aciklamalar 
        WHERE resim is not null and durum = 1 ORDER BY RAND() LIMIT 1")->getOne()["sef_link"];
    }


    public function GetExplainsWithCategory($category_slug,$userid){
        return $this->set_sql($this->where_sql(''," WHERE ac.durum = 1 and ka.kategori_sef = '".$category_slug."' ",$userid))->getAll();
    }
    public function GetReportItems(){
        return $this->from('durumlar')->where('tablo','aciklama_bildir')->getAll();
    }
    public function CheckReportItem($id){
        return !empty($this->from('durumlar')->where(['id'=>$id,'tablo'=>'aciklama_bildir'])->getOne());
    }
    public function AddReport($params){
        return (bool) $this->from('aciklama_bildir')->insert2($params);
    }
    public function AddExplainView($explain_id){
        return (bool) $this->from('aciklamalar')->set(" goruntulenme = goruntulenme + 1 ")->where('id',$explain_id)->update2();
    }
    public function AddComment($params){
        $result = (bool) $this->from('yorumlar')->insert2($params);
        if($result) return $this->db->lastInsertId();
        return 0;
    }

    private function where_sql($columns = "",$where = "",$userid = 0,$start = null,$limit = null){

        if(!empty($columns)) $columns = ",".$columns;

        $sql = "SELECT ac.id,ac.kategori_id,ac.sef_link,CONCAT(ku.ad, ' ',ku.soyad) as adsoyad,ku.kullanici_adi,ku.tip,
        ac.kullanici_id,
        ka.kategori_adi,ka.icon as kategori_icon,ka.kategori_sef,ac.baslik,ac.paylasim_tarih,ac.kaynak,ac.paylasim_tarih,ac.yorum_izin,ac.aciklama_tip,ac.icerik,
        IF(ku.uzak_avatar != '',ku.uzak_avatar,ku.avatar) as avatar,
        IFNULL(yo.toplam_yorum,0) as yorumsayisi,IFNULL(be.toplam_begeni,0) as begenisayisi,
        IF(ben.id IS NULL,0,1) as begendim,
        IF(kay.id IS NULL,0,1) as kaydettim,
        IFNULL(kydt.toplam_kaydetme,0) as kaydetmesayisi {$columns},
        ac.resim,
        ac.goruntulenme
        FROM aciklamalar ac
        LEFT JOIN kullanicilar ku ON ac.kullanici_id = ku.id 
        LEFT JOIN kategoriler ka ON ka.id = ac.kategori_id
        LEFT JOIN ( SELECT COUNT(y.id) as toplam_yorum,y.aciklama_id FROM yorumlar y WHERE y.durum = 1 GROUP BY y.aciklama_id ) as yo ON yo.aciklama_id = ac.id
        LEFT JOIN ( SELECT COUNT(b.id) as toplam_begeni,b.aciklama_id FROM begeniler b GROUP BY b.aciklama_id ) as be ON be.aciklama_id = ac.id
        LEFT JOIN ( SELECT COUNT(k.id) as toplam_kaydetme,k.aciklama_id FROM kaydetmeler k GROUP BY k.aciklama_id ) as kydt ON kydt.aciklama_id = ac.id
         
        LEFT JOIN begeniler ben on ben.aciklama_id = ac.id and ben.kullanici_id = {$userid}
        LEFT JOIN kaydetmeler kay on kay.aciklama_id = ac.id and kay.kullanici_id = {$userid}

        
        {$where}

        ORDER BY ac.paylasim_tarih DESC

        
        ";
        if(is_null($start) || is_null($limit)){
            $sql .= "LIMIT 0,10";
        }else{
            $sql .= "LIMIT {$start},{$limit}";
        }


        return $sql;
    }

    public function GetInterestingExplanations(){
        $sql = "SELECT a.sef_link,a.baslik FROM aciklamalar a
        LEFT JOIN 
            (SELECT b.aciklama_id,COUNT(b.id) as begentotal FROM begeniler b WHERE b.tarih >= NOW() - INTERVAL 1 DAY GROUP BY b.aciklama_id) b 
            ON b.aciklama_id = a.id
        LEFT JOIN
            (SELECT y.aciklama_id,COUNT(y.id) as yorumtotal FROM yorumlar y  WHERE y.tarih >= NOW() - INTERVAL 1 DAY GROUP BY y.aciklama_id) y
            ON y.aciklama_id = a.id
        LEFT JOIN
            (SELECT k.aciklama_id,COUNT(k.id) as kaydettotal FROM yorumlar k  WHERE k.tarih >= NOW() - INTERVAL 1 DAY GROUP BY k.aciklama_id) k
            ON k.aciklama_id = a.id
            
        WHERE a.durum = 1
                
        ORDER BY y.yorumtotal DESC,b.begentotal DESC,k.kaydettotal DESC,a.goruntulenme DESC
        
        LIMIT 3";
        return $this->set_sql($sql)->getAll();
    }

    public function GetInterestingExplanationsWithCategory($kategori){
        $sql = "SELECT a.sef_link,a.baslik FROM aciklamalar a
        LEFT JOIN 
            kategoriler ka ON ka.id = a.kategori_id
        LEFT JOIN 
            (SELECT b.aciklama_id,COUNT(b.id) as begentotal FROM begeniler b WHERE b.tarih >= NOW() - INTERVAL 1 DAY GROUP BY b.aciklama_id) b 
            ON b.aciklama_id = a.id
        LEFT JOIN
            (SELECT y.aciklama_id,COUNT(y.id) as yorumtotal FROM yorumlar y  WHERE y.tarih >= NOW() - INTERVAL 1 DAY GROUP BY y.aciklama_id) y
            ON y.aciklama_id = a.id
        LEFT JOIN
            (SELECT k.aciklama_id,COUNT(k.id) as kaydettotal FROM yorumlar k  WHERE k.tarih >= NOW() - INTERVAL 1 DAY GROUP BY k.aciklama_id) k
            ON k.aciklama_id = a.id
            
        WHERE a.durum = 1 and ka.kategori_sef = '{$kategori}'
                
        ORDER BY y.yorumtotal DESC,b.begentotal DESC,k.kaydettotal DESC,a.goruntulenme DESC
        
        LIMIT 3";
        return $this->set_sql($sql)->getAll();
    }

    public function FindCategoryId($kategori){
        return $this->select('id')->from('kategoriler')->where("kategori_adi LIKE '%{$kategori}%'")->getOne()['id'];
    }
    public function GetDefaultCategoryId(){
        return $this->select('id')->from('kategoriler')->where("kategori_adi LIKE '%gündem%'")->getOne()['id'];
    }
    public function RemoveExplain($id){
        return (bool)$this->from('aciklamalar')->set(["durum"=>0])->where('id',$id)->update2();
    }
    public function GetTotalCount(){
        return $this->from('aciklamalar')->count();
    }
    public function LogUpdate($userid,$aciklama_id,$type){
        return (bool)$this->from('aciklamalar_islem ')->insert2([
            "kullanici_id"=>$userid,
            "tag_id"=>$aciklama_id,
            "tip"=>"UPDATE:".strtoupper($type)
        ]);
    }
    public function LogGoruntuleme($userid,$aciklama_id,$type){
        return (bool)$this->from('aciklamalar_islem ')->insert2([
            "kullanici_id"=>$userid,
            "tag_id"=>$aciklama_id,
            "tip"=>"GORUNTULEME:".strtoupper($type)
        ]);
    }
    public function GetCategoryIdWithSlug($slug){
        return $this->from('kategoriler')->where('kategori_sef',$slug)->getOne()['id'];
    }
    public function AddMiniExplain($data){
        return $this->from('aciklamalar')->insert2($data);
    }
    

}