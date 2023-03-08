<?php

namespace App\Controllers;

class ImageController extends BaseController
{
    public function imageUploads($image)
    {
        $filePath = ROOTPATH."writable/uploads/images/$image";
        if (file_exists($filePath)){
            $mime = mime_content_type($filePath);
            header("Content-Type: $mime");
            header('Cache-Control: max-age=86400');
            readfile($filePath);
            die();
        } else {
            http_response_code(404);
            die();
        }
    }

}
