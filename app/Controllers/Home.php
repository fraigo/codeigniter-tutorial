<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        helper("auth");
        if (getenv('app.private')=="true" && !user_id()) {
            http_response_code(getenv('app.private_response')?:404);
            die();    
        }    
        $pages = new \App\Models\Pages();
        $page = $pages->find(getenv('app.homepage')?:1);
        if (!$page){
            http_response_code(404);
            die(); 
        }
        return $this->layout('page',$page);
    }

}
