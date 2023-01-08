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
            ["tag" => "a", "attributes" => [ 
                'href' => '/users/delete/{id}',
                'onclick' => "return confirm('Are you sure you want to delete this item?')"
            ], "content" => '🗑'],
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
        return view('users/view',['item'=>$item,'title'=>'View User']);
    }

    function edit($id){
        $model = new \App\Models\Users();
        $item = $model->where('id',$id)->first();
        $item['password'] = '';
        return view('users/form',['item'=>$item, 'errors'=>$this->errors,'title'=>'Edit User']);
    }

    function update($id){
        $model = new \App\Models\Users();
        $item = $model->where('id',$id)->first();
        $has_password = $this->request->getVar('password');
        $fields = ['name','email','password','repeat_password'];
        if (!$has_password){
            $fields = ['name','email'];
        }
        $data = $this->request->getVar($fields);
        $data["id"] = $id;
        $rules = $model->getValidationRules(['only'=>$fields]);
        if ($has_password){
            $rules['repeat_password'] = 'matches[password]';
        }
        $validation = \Config\Services::validation();
        $validation->setRules($rules);
        if (!$validation->run($data)){
            $this->errors = $validation->getErrors();
            return view('users/form',['item'=>$item, 'errors'=>$this->errors]);
        }
        if ($has_password){
            $data["password"] = md5($data["password"]);
        }
        $model->update($item["id"],$data);
        return $this->response->redirect('/users/edit/'.$data['id']);
    }

    function new(){
        $model = new \App\Models\Users();
        $item = [];
        return view('users/form',['item'=>$item]);
    }

    function create(){
        $model = new \App\Models\Users();
        $data = $this->request->getVar(['name','email','password','repeat_password']);
        $data["user_type"] = 0;
        $rules = $model->getValidationRules(['only'=>['name','email','password','repeat_password']]);
        $rules['repeat_password'] = 'matches[password]';
        $validation = \Config\Services::validation();
        $validation->setRules($rules);
        if (!$validation->run($data)){
            $this->errors = $validation->getErrors();
            return view('users/form',['item'=>$data, 'errors'=>$this->errors]);
        }
        $data["password"] = md5($data["password"]);
        $id = $model->insert($data);
        return $this->response->redirect('/users/edit/'.$id);
    }

    function delete($id){
        $model = new \App\Models\Users();
        $model->delete($id);
        return redirect()->back();
    }

}
