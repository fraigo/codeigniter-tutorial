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
        $pageId = getenv('app.homepage')?:1;
        $page = $pages->find($pageId);
        $userTypePage = $pages->where([
            'slug'=>'home_page_'.current_user()['user_type']
        ])->first();
        if ($userTypePage){
            $page = $userTypePage;
        }
        if (!$page){
            http_response_code(404);
            die(); 
        }
        $page['contents'] = str_replace('{today}',date('Y-m-d'),$page['contents']);
        $page['contents'] = str_replace('{yesterday}',date('Y-m-d',strtotime('-1 day')),$page['contents']);
        return $this->layout('page',$page);
    }

}
