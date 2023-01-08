<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Users extends BaseController
{
    protected $helpers = ['html'];
    protected $errors = null;

    public function index()
    {
        $model = new \App\Models\Users();
        $pagerGroup = 'users';
        $pageSize = @$_GET["pagesize_$pagerGroup"]?:10;
        $items = $model->select(['id','name','email','updated_at'])->paginate($pageSize,$pagerGroup);
        $actions = [
            ["tag" => "a", "attributes" => [ 'href' => '/users/view/{id}'], "content" => '👁'],
            ["tag" => "a", "attributes" => [ 'href' => '/users/edit/{id}'], "content" => '✏️'],
        ];
        $columns = [
            "actions" => [
                "content" => $actions,
                "cellAttributes" => [
                    "class" => "actions",
                    "width" => "100"
                ]
            ],
            "name" => "Name", 
            "email" => "E-mail", 
            "updated_at" => "Last update",
        ];
        return view('users/index',[
            "title" => "Users", // page $title
            "items" => $items,
            "columns" => $columns,
            "pager" => $model->pager,
            "pagesize" => $pageSize,
            "pager_group" => $pagerGroup
        ]);
    }


    function view($id){
        $model = new \App\Models\Users();
        $item = $model->where('id',$id)->first();
        return view('users/view',['item'=>$item]);
    }

    function edit($id){
        $model = new \App\Models\Users();
        $item = $model->where('id',$id)->first();
        return view('users/form',['item'=>$item, 'errors'=>$this->errors]);
    }

    function update($id){
        $model = new \App\Models\Users();
        $item = $model->where('id',$id)->first();
        $data = $this->request->getVar(['name','email']);
        $data["id"] = $id;
        $rules = $model->getValidationRules(['only'=>['name','email']]);
        $validation = \Config\Services::validation();
        $validation->setRules($rules);
        if (!$validation->run($data)){
            $this->errors = $validation->getErrors();
            return view('users/form',['item'=>$item, 'errors'=>$this->errors]);
        }
        $model->update($item["id"],$data);
        return $this->response->redirect('/users/edit/'.$data['id']);
    }

}
