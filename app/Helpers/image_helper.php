<?php

function png2jpg($filePath, $newname=null, $quality=70){
    $image = imagecreatefrompng($filePath);
    $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
    imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
    imagealphablending($bg, TRUE);
    imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
    imagedestroy($image);
    imagejpeg($bg, $newname?:str_ireplace(".png",".jpg",$filePath), $quality);
    imagedestroy($bg);
}