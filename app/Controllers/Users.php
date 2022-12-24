<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Users extends BaseController
{
    public function index()
    {
        $model = new \App\Models\Users();
        $items = $model->findAll();
        return $this->response->setJSON($items);
    }
}
