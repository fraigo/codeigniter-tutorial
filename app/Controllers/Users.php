<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Users extends BaseController
{
    protected $modelName = 'App\Models\Users';
    protected $fields = [
        "id" => [
            "label" => "ID"
        ],
        "name" => [
            "label" => "Name",
            "sort" => true
        ],
        "email" => [
            "label" => "Email",
            "sort" => true
        ],
        "updated_at" => [
            "label" => "Last Update",
            "sort" => true
        ],
        "login_at" => [
            "label" => "Last Login"
        ]
    ];
    
    public function index()
    {
        $pagerGroup = 'users';
        $pageSize = @$_GET["pagesize_$pagerGroup"]?:10;
        $query = $this->model
            ->select($this->selectFields());
        $filters = $this->processFilters($query,['name','email'],$pagerGroup);
        $this->processSort($query,$pagerGroup);
        $items = $query->paginate($pageSize,$pagerGroup);
        $columns = $this->indexColumns(['name','email','updated_at'],'/users',$pagerGroup);

        return $this->layout('users/index',[
            "title" => "Users", // page $title
            "items" => $items,
            "columns" => $columns,
            "filters" => $filters,
            "pager" => $this->model->pager,
            "pagesize" => $pageSize,
            "pager_group" => $pagerGroup
        ]);
    }


    function view($id){
        $item = $this->getModelById($id);
        return $this->parserLayout('users/view',['item'=>[$item],'title'=>'View User','editurl' => '/users/edit/'.$item['id']]);
    }

    function edit($id){
        $item = $this->getModelById($id);
        $item['password'] = '';
        return $this->layout('users/form',['item'=>$item, 'errors'=>$this->errors,'title'=>'Edit User']);
    }

    function update($id){
        $item = $this->getModelById($id);
        $has_password = $this->request->getVar('password');
        $fields = ['name','email'];
        if ($has_password){
            $fields = array_merge($fields,['password','repeat_password']);
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
        return $this->layout('users/form',['item'=>$item]);
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
