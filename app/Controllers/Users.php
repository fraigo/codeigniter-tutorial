<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Users extends BaseController
{

    protected $helpers = ['html'];

    public function index()
    {
        $model = new \App\Models\Users();
        $items = $model->findAll();
        return view('users/index',[
            "items"=>$items
        ]);
    }
}
