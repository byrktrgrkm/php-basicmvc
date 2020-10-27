<?php

defined('BASEPATH') OR exit('No direct script access allowed');

abstract class UserAuth{
    public const ONAYBEKLEYEN = 0;
    public const AKTIF = 1;
    public const PASIF = 2;
    public const YASAKLI = 3;
    public const DONDURMUS = 4;
    public const EDITOR = 5;
    public const MODERATOR = 6;
    public const YETKILI = 7;
    public const SUPERYETKILI = 9;
}

class User extends Model{

    private $table = "kullanicilar";

    private $KullaniciDurumlari = [
        0 => "onay-bekleyen",
        1 => "aktif", 
        2 => "pasif",
        3 => "yasakli",
        4 => "dondurmus",
        5 => "Editor",
        6 => "Moderator",
        7 => "yetkili",
        9 => "superyetki"
    ];

    private function Durum($id){
        return  isset($this->KullaniciDurumlari[$id]) ? $this->KullaniciDurumlari[$id] : "";
    }
    public function DurumOnay($id){
        return UserAuth::ONAYBEKLEYEN == $id;
        //return $this->Durum($id) == 'onay-bekleyen';
    }
    public function DurumAktif($id){
        return UserAuth::AKTIF == $id;
        //return $this->Durum($id) == 'aktif';
    }
    public function DurumPasif($id){
        return UserAuth::PASIF == $id;
        //return $this->Durum($id) == "pasif";
    }
    public function DurumYasak($id){
        return UserAuth::YASAKLI == $id;
    }
    public function DurumDonuk($id){
        return UserAuth::DONDURMUS == $id;
        //return $this->Durum($id) == "dondurmus";
    }
    public function DurumEditor($id){
        return UserAuth::EDITOR == $id;
    }
    public function DurumModerator($id){
        return UserAuth::MODERATOR == $id;
    }
    public function DurumYetki($id){
        return UserAuth::YETKILI == $id;
        //return $this->Durum($id) == "yetkili";
    }
    public function DurumSuperYetki($id){
        return UserAuth::SUPERYETKILI == $id;
        //return $this->Durum($id) == "superyetki";
    }
    public function GetUserPowers(){
        return $this->from('kullanici_yetki')->getAll();
    }
 
    public function AddBot($params)
    {
        return (bool)$this->from($this->table)->insert2($params);
        # code...
    }
    public function AddUser($params){
        return (bool)$this->from($this->table)->insert2($params);
    }
    public function CheckMail($mail){
        return !empty($this->from($this->table)->where('email',$mail)->getOne());
    }
    public function GetUserInfo($username,$password){
        $sql = "SELECT k.id,k.kullanici_adi,CONCAT(k.ad,' ',k.soyad) as adsoyad,k.durum,
        IF(k.uzak_avatar != '',k.uzak_avatar,k.avatar) as avatar 
        FROM kullanicilar k 
        WHERE (k.email = '{$username}' or k.kullanici_adi = '{$username}' ) and k.sifre = '{$password}' and k.tip = 'USER'
        ";

        return $this->set_sql($sql)->getOne();
    }
    public function CheckSession($sess){
        $sql = "SELECT k.id,k.kullanici_adi,CONCAT(k.ad,' ',k.soyad) as adsoyad,k.durum,
        IF(k.uzak_avatar != '',k.uzak_avatar,k.avatar) as avatar 
        FROM kullanicilar k 
        WHERE k.sess = '{$sess}' and k.tip = 'USER'
        limit 1
        ";

        return $this->set_sql($sql)->getOne();
    }
    public function CheckCommentId($comment_id){
        return !empty($this->select('id')->from('yorumlar')->where(['id'=>$comment_id,'durum'=>1])->getOne());
    }
    public function GetComment($comment_id){
        $sql = "SELECT y.*,a.sef_link FROM yorumlar y LEFT JOIN  aciklamalar a ON a.id = y.aciklama_id 
        
        WHERE y.durum = 1 and y.id = {$comment_id}
        ";
        return $this->set_sql($sql)->getOne();
    }
    public function UpdateComment($icerik,$comment_id){
        return (bool) $this->from('yorumlar')->set(["icerik"=>$icerik])->where("id",$comment_id)->update2();
    }

    public function CheckCommentUserId($comment_id,$kullanici_id){
        return !empty($this->from('yorumlar')->where(["id"=>$comment_id,"kullanici_id"=>$kullanici_id])->getOne());
    }
    public function RemoveCommentWithId($comment_id){
        return (bool)$this->from('yorumlar')->set('durum',-1)->where(['id'=>$comment_id,'ust_id'=>$comment_id],null,'or')->update2();
        //return (bool)$this->from('yorumlar')->where(['id'=>$comment_id])->del();
    }

    public function GetUserInfoById($id){
        $sql = "SELECT k.id,k.kullanici_adi,CONCAT(k.ad,' ',k.soyad) as adsoyad,k.durum,k.email,
        IF(k.uzak_avatar != '',k.uzak_avatar,k.avatar) as avatar 
        FROM kullanicilar k 
        WHERE k.id = '{$id}'
        ";

        return $this->set_sql($sql)->getOne();
    }
    public function GetUserProfile($id){
    
        return $this->select(" k.id,k.kullanici_adi,CONCAT(k.ad,' ',k.soyad) as adsoyad,
        k.email,k.durum,k.uzak_avatar,k.avatar,k.ad,k.soyad ")->from('kullanicilar k')->where('k.id',$id)->getOne();
    }

    public function availableUsername($username){
        return !empty($this->from("kullanicilar")->where('kullanici_adi',$username)->getOne());
    }
    public function saveProfile($params,$id){
        return (bool) $this->from("kullanicilar")->set($params)->where('id',$id)->update2();
    }

    public function getAvatar($userid){

        return $this->select(' avatar ')->from('kullanicilar')->where('id',$userid)->getOne()['avatar'];
    }
    public function saveAvatar($avatarname,$userid){
        return (bool)$this->from('kullanicilar')->set("avatar",$avatarname)->where('id',$userid)->update2();
    }
    public function EmailInfo($email){
        return $this->select('CONCAT(ad," ",soyad) as adsoyad,anahtar,email')->from('kullanicilar')->where(["email"=>$email,"tip"=>"USER"])->getOne();
    }
    public function CheckEmailCode($code,$durum = 0){
        return !empty($this->from('kullanicilar')->where(["anahtar"=>$code,"durum"=>$durum])->getOne());
    }
    public function EmailCode($code){
        return $this->select('email')->from('kullanicilar')->where("anahtar",$code)->getOne();
    }
    public function ConfirmEmail($code,$newcode){
        return (bool)$this->from('kullanicilar')->set(["anahtar"=>$newcode,"durum"=>1])->where('anahtar',$code)->update2();
    }
    public function ChangeUserKey($email,$newcode){
        return (bool)$this->from('kullanicilar')->set("anahtar",$newcode)->where('email',$email)->update2();
    }
    public function ChangePasswordWithToken($password,$code,$newcode){
        return (bool)$this->from('kullanicilar')->set(["anahtar"=>$newcode,"sifre"=>$password])->where('anahtar',$code)->update2();
    }

    public function CheckGoogleProfile($id,$email){
        return $this->select('*,CONCAT(ad," ",soyad) as adsoyad ')->from($this->table)->where(["googleid"=>$id,"email"=>$email])->getOne();
    }
    public function GetLastInsertId(){
        return $this->db->lastInsertId();
    }

    public function UserPassword($id){
        return $this->select('sifre')->from('kullanicilar')->where('id',$id)->getOne()['sifre'];
    }
    public function UpdateProfile($user_id,$data){
        return (bool)$this->from('kullanicilar')->set($data)->where('id',$user_id)->update2();
    }

    public function ChangePassword($userid,$password){
        return (bool) $this->from('kullanicilar')->set(['sifre'=>$password])->where('id',$userid)->update2();
    }
    public function AccountFreeze($id){
        return (bool)$this->from('kullanicilar')->set(['durum'=>4])->where('id',$id)->update2();
    }
    public function AccountConfirm($id,$confirm){
        return (bool)$this->from('kullanicilar')->where('id',$id)->set('durum',$confirm)->update2();
    }
    
    public function SetSession($userid,$key){
        return (bool)$this->from('kullanicilar')->where('id',$userid)->set('sess',$key)->update2();
    }
    public function LogLogin($userid){
        return (bool)$this->from('kullanicilar_islem')->insert2([
            "kullanici_id"=>$userid,
            "tip"=>"LOGIN"
        ]);
    }
    public function LogUpdate($userid,$type){
        return (bool)$this->from('kullanicilar_islem')->insert2([
            "kullanici_id"=>$userid,
            "tip"=>"UPDATE:".strtoupper($type)
        ]);
    }
    public function GetNotificationNews($userid){
        $sql = "SELECT b.id,CONCAT(k.ad,' ',k.soyad) as gonderen_adsoyad,b.href,bt.name as tip,bt.icon,IFNULL(k.uzak_avatar,k.avatar) as avatar,b.baslik_html,b.govde_html,b.gonderen_id,b.olusturma_tarih  FROM bildirim b 
        LEFT JOIN bildirim_tip bt ON bt.id = b.bildiri_tip
        LEFT JOIN kullanicilar k ON k.id = b.gonderen_id
        WHERE b.okundu = 0 AND b.gizli = 0 AND b.alici_id = {$userid}
        ORDER BY b.olusturma_tarih DESC";
        return $this->set_sql($sql)->getAll();
    }
    public function GetNotificationPrevious($userid){
        $sql = "SELECT b.id,CONCAT(k.ad,' ',k.soyad) as gonderen_adsoyad,b.href,bt.name as tip,bt.icon,IFNULL(k.uzak_avatar,k.avatar) as avatar,b.baslik_html,b.govde_html,b.gonderen_id,b.olusturma_tarih  FROM bildirim b 
        LEFT JOIN bildirim_tip bt ON bt.id = b.bildiri_tip
        LEFT JOIN kullanicilar k ON k.id = b.gonderen_id
        WHERE b.okundu = 1 AND b.gizli = 0 AND b.alici_id = {$userid}
        ORDER BY b.olusturma_tarih DESC
        LIMIT 10
        ";
        return $this->set_sql($sql)->getAll();
    }
    public function SetNotificationAllRead($userid,$datetime){
        return $this->from('bildirim')->where([
            "alici_id"=>$userid,
            "olusturma_tarih"=>[
                "val"=>$datetime,
                "operation"=>"<="
            ],
            "okundu"=>0,
            "gizli"=>0
        ])->set([
            "okuma_tarih"=>$datetime,
            "okundu"=>1
        ])->update2();
    }
    public function GetNotificationUser($userid,$datetime){
        $sql = "SELECT b.id,CONCAT(k.ad,' ',k.soyad) as gonderen_adsoyad,b.href,bt.name as tip,bt.icon,IFNULL(k.uzak_avatar,k.avatar) as avatar,b.baslik_html,b.govde_html,b.gonderen_id,b.olusturma_tarih  FROM bildirim b 
        LEFT JOIN bildirim_tip bt ON bt.id = b.bildiri_tip
        LEFT JOIN kullanicilar k ON k.id = b.gonderen_id
        WHERE b.okundu = 0 AND b.gizli = 0 AND b.alici_id = {$userid} AND b.olusturma_tarih > '{$datetime}' ";
        return $this->set_sql($sql)->getAll();
    }

    public function FB_GetUser($id){
        return $this->from('kullanicilar')->where('facebook_id',$id)->getOne();
    }
    public function FB_Insert($id,$firtname,$lastname,$email,$picture_url,$kullanici_adi){
        $insert =  $this->from('kullanicilar')->insert2([
            "facebook_id"=>$id,
            "provider"=>"facebook",
            "kullanici_adi"=>$kullanici_adi,
            "ad"=>$firtname,
            "soyad"=>$lastname,
            "email"=>$email,
            "durum"=>1,
            "avatar"=>$picture_url,
            "tip"=>"USER"
        ]);
        if($insert){
            return $this->GetLastInsertId();
        }else{
            return false;
        }
    }
    public function Google_GetUser($id){
        return $this->from('kullanicilar')->where('googleid',$id)->getOne();
    }
    public function Google_Insert($id,$firtname,$lastname,$email,$picture_url,$kullanici_adi){
        $insert =  $this->from('kullanicilar')->insert2([
            "googleid"=>$id,
            "provider"=>"google",
            "kullanici_adi"=>$kullanici_adi,
            "ad"=>$firtname,
            "soyad"=>$lastname,
            "email"=>$email,
            "durum"=>1,
            "avatar"=>$picture_url,
            "tip"=>"USER"
        ]);
        if($insert){
            return $this->GetLastInsertId();
        }else{
            return false;
        }
    }
    public function GetExplanionList($userid,$offset,$pagination){
        $sql = "SELECT a.baslik,a.sef_link,a.id,a.durum,a.guncelleme_tarih,a.paylasim_tarih,
        a.goruntulenme,CONCAT(k.ad,' ',k.soyad) as adsoyad,ka.kategori_adi,ka.icon,a.aciklama_tip,a.yorum_izin,
        IFNULL(b.toplam,0) as toplambegeni,
        IFNULL(y.toplam,0) as toplamyorum 
        FROM aciklamalar a
        LEFT JOIN kullanicilar k ON k.id = a.kullanici_id
        LEFT JOIN kategoriler ka ON ka.id = a.kategori_id
        LEFT JOIN (SELECT b.aciklama_id,COUNT(b.id) as toplam FROM begeniler b GROUP BY b.aciklama_id) b ON b.aciklama_id = a.id
        LEFT JOIN (SELECT y.aciklama_id,COUNT(y.id) as toplam FROM yorumlar y GROUP BY y.aciklama_id) y ON y.aciklama_id = a.id
        
        WHERE a.durum = 1 and a.kullanici_id = {$userid}
        ORDER BY a.paylasim_tarih DESC
        LIMIT {$offset},{$pagination}
        ";
        return $this->set_sql($sql)->getAll();
    }
    public function GetExplanionQuery($userid,$q){
        $x = "%".trim($q)."%";
        $sql = "SELECT a.baslik,a.sef_link,a.id,a.durum,a.guncelleme_tarih,a.paylasim_tarih,
        a.goruntulenme,CONCAT(k.ad,' ',k.soyad) as adsoyad,ka.kategori_adi,ka.icon,a.aciklama_tip,a.yorum_izin,
        IFNULL(b.toplam,0) as toplambegeni,
        IFNULL(y.toplam,0) as toplamyorum 
        FROM aciklamalar a
        LEFT JOIN kullanicilar k ON k.id = a.kullanici_id
        LEFT JOIN kategoriler ka ON ka.id = a.kategori_id
        LEFT JOIN (SELECT b.aciklama_id,COUNT(b.id) as toplam FROM begeniler b GROUP BY b.aciklama_id) b ON b.aciklama_id = a.id
        LEFT JOIN (SELECT y.aciklama_id,COUNT(y.id) as toplam FROM yorumlar y GROUP BY y.aciklama_id) y ON y.aciklama_id = a.id
        
        WHERE a.durum = 1 and a.kullanici_id = {$userid} and (a.baslik like :bul or a.icerik like :bul)
        ORDER BY a.paylasim_tarih DESC
        ";
        return $this->set_sql($sql)->bind2(["bul"=>$x])->getAllSecurity();
        
    }
    public function GetExplain($id){
        return $this->select('*')->from("aciklamalar")->where('id',$id)->getOne();
    }
    
    public function AddExplain($data){
        return (bool)$this->from('aciklamalar')->insert2($data);
    }
    public function EditExplain($id,$data){
        return (bool)$this->from('aciklamalar')->where('id',$id)->set($data)->update2();
    }
    public function GetExplainCount($userid){
        return $this->from('aciklamalar')->where(["durum"=>1,"kullanici_id"=>$userid])->count();
    }
    public function GetUsersLikePosts($explain_id){
        $sql = "SELECT CONCAT(k.ad,' ',k.soyad) as adsoyad,k.avatar,k.durum FROM begeniler b 
        LEFT JOIN kullanicilar k ON k.id = b.kullanici_id
        LEFT JOIN aciklamalar a ON a.id = b.aciklama_id
        WHERE b.aciklama_id = {$explain_id} and a.durum = 1 ";
        return $this->set_sql($sql)->getAll();
    }
    public function GetUsersBookmarkPosts($explain_id){
        $sql = "SELECT CONCAT(k.ad,' ',k.soyad) as adsoyad,k.avatar,k.durum FROM kaydetmeler b 
        LEFT JOIN kullanicilar k ON k.id = b.kullanici_id
        LEFT JOIN aciklamalar a ON a.id = b.aciklama_id
        WHERE b.aciklama_id = {$explain_id} and a.durum = 1 ";
        return $this->set_sql($sql)->getAll();
    }
    public function VoteComment($comment_id,$user_id,$vote){
        return (bool)$this->from('yorum_oylar')->insert2([
            "yorum_id"=>$comment_id,
            "kullanici_id"=>$user_id,
            "durum"=>$vote
        ]);
    }
    public function VoteCheckComment($comment_id,$user_id){
        return $this->select('durum')->from('yorum_oylar')->where([
            "yorum_id"=>$comment_id,
            "kullanici_id"=>$user_id
        ])->getOne();
    }
    public function VoteRemoveComment($comment_id,$user_id){
        return $this->from('yorum_oylar')->where([
            "yorum_id"=>$comment_id,
            "kullanici_id"=>$user_id
        ])->del();
    }

}