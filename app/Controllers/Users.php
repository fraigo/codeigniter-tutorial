<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Users extends BaseController
{
    protected $helpers = ['html'];
    protected $errors = null;
    protected $modelName = 'App\Models\Users';

    public function index()
    {
        $pagerGroup = 'users';
        $pageSize = @$_GET["pagesize_$pagerGroup"]?:10;
        
        $query = $this->model->select(['id','name','email','updated_at']);
        
        $sortQuery = "sort_$pagerGroup";
        $sort=$this->request->getVar($sortQuery);
        $sortUrl = current_url(true);
        $sortUrl->stripQuery($sortQuery);
        if ($sort) $query->orderBy($sort);
        
        $items = $query->paginate($pageSize,$pagerGroup);
        $actions = [
            ["tag" => "a", "attributes" => [ 'class' => 'px-sm-1', 'href' => '/users/view/{id}'], "content" => '👁'],
            ["tag" => "a", "attributes" => [ 'class' => 'px-sm-1', 'href' => '/users/edit/{id}'], "content" => '✏️'],
            ["tag" => "a", "attributes" => [ 
                'class' => 'px-sm-1', 
                'href' => '/users/delete/{id}',
                'onclick' => "return confirm('Are you sure you want to delete this item?')"
            ], "content" => '🗑'],
        ];
        $columns = [
            "actions" => [
                "content" => $actions,
                "cellAttributes" => [
                    "class" => "actions text-center text-nowrap",
                    "width" => "100"
                ]
            ],
            "name" => [
                "label" => anchor(
                    $sortUrl->addQuery($sortQuery,$sort=='name'?'name desc':'name')->__toString(),
                    "Name".($sort=='name'?' ↓':($sort=='name desc'?' ↑':''))
                )
            ],
            "email" => [
                "label" => anchor(
                    $sortUrl->addQuery($sortQuery,$sort=='email'?'email desc':'email')->__toString(),
                    "Email".($sort=='email'?' ↓':($sort=='email desc'?' ↑':''))
                ) 
            ],
            "updated_at" => [
                "label" => anchor(
                    $sortUrl->addQuery($sortQuery,$sort=='updated_at'?'updated_at desc':'updated_at')->__toString(),
                    "Last Update".($sort=='updated_at'?' ↓':($sort=='updated_at desc'?' ↑':''))
                )
            ]
        ];
        return view('users/index',[
            "title" => "Users", // page $title
            "items" => $items,
            "columns" => $columns,
            "pager" => $this->model->pager,
            "pagesize" => $pageSize,
            "pager_group" => $pagerGroup
        ]);
    }


    function view($id){
        $item = $this->getModelById($id);
        return view('users/view',['item'=>$item,'title'=>'View User']);
    }

    function edit($id){
        $item = $this->getModelById($id);
        $item['password'] = '';
        return view('users/form',['item'=>$item, 'errors'=>$this->errors,'title'=>'Edit User']);
    }

    function update($id){
        $item = $this->getModelById($id);
        $has_password = $this->request->getVar('password');
        $fields = ['name','email','password','repeat_password'];
        if (!$has_password){
            $fields = ['name','email'];
        }
        $data = $this->request->getVar($fields);
        $data["id"] = $id;
        $rules = $this->model->getValidationRules(['only'=>$fields]);
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
        $this->model->update($item["id"],$data);
        return $this->response->redirect('/users/edit/'.$data['id']);
    }

    function new(){
        $item = [];
        return view('users/form',['item'=>$item]);
    }

    function create(){
        $data = $this->request->getVar(['name','email','password','repeat_password']);
        $data["user_type"] = 0;
        $rules = $this->model->getValidationRules(['only'=>['name','email','password','repeat_password']]);
        $rules['repeat_password'] = 'matches[password]';
        $validation = \Config\Services::validation();
        $validation->setRules($rules);
        if (!$validation->run($data)){
            $this->errors = $validation->getErrors();
            return view('users/form',['item'=>$data, 'errors'=>$this->errors]);
        }
        $data["password"] = md5($data["password"]);
        $id = $this->model->insert($data);
        return $this->response->redirect('/users/edit/'.$id);
    }

    function delete($id){
        $item = $this->getModelById($id);
        $this->model->delete($id);
        return redirect()->back();
    }

}
