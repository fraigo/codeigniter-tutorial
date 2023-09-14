<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        $pages = new \App\Models\Pages();
        $page = $pages->find(1);
        return $this->layout('page',$page);
    }

}
