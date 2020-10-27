<?php 



function get_file_upload_error($errorid){
    $phpFileUploadErrors = array(
        0 => 'Yükleme başarılı,hata yok',
        1 => '.ini dosyasında belirtilen maximum dosya boyutunu aştınız.',
        2 => 'Html form üzerinde belirtilen maximum dosya boyutunu aştınız',
        3 => 'Dosya hatalı yüklendi.',
        4 => 'Dosya yüklenmedi',
        6 => 'Missing a temporary folder',
        7 => 'Failed to write file to disk.',
        8 => 'A PHP extension stopped the file upload.',
    );
    if(is_numeric($errorid) && $errorid <= 8)
        return $phpFileUploadErrors[$errorid];

    return "Hata aralık dışındaydı.(get_file_upload_error)";
}


/**
* Resize an image and keep the proportions
* @author Allison Beckwith <allison@planetargon.com>
* @param string $filename
* @param integer $max_width
* @param integer $max_height
* @return image
*/
function avatar_resizeImage($filetype,$filename,$source_file,$max_width, $max_height)
{
    ini_set('display_startup_errors', 1);
    ini_set('display_errors', 1);
    error_reporting(-1);
    
    list($orig_width, $orig_height) = getimagesize($filename);

    $width = $orig_width;
    $height = $orig_height;

    # taller
    if ($height > $max_height) {
        $width = ($max_height / $height) * $width;
        $height = $max_height;
    }

    # wider
    if ($width > $max_width) {
        $height = ($max_width / $width) * $height;
        $width = $max_width;
    }

    $image_p = imagecreatetruecolor($width, $height);

    $white = imagecolorallocate($image_p, 255, 255, 255);
    imagefill($image_p, 0, 0, $white);

    if($filetype == "png"){
        $image = imagecreatefrompng($filename);
    }else if($filetype == "jpg" || $filetype == "jpeg"){
        $image = imagecreatefromjpeg($filename);
    }
    
    

    imagecopyresampled($image_p, $image, 0, 0, 0, 0,
                                     $width, $height, $orig_width, $orig_height);

    imagejpeg($image_p,$source_file);

    unset($filename);
}




function base_upload_path($path = ""){
    return dirname(__DIR__)."/".$path;
}

