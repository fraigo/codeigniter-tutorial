<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Users extends BaseController
{
    protected $modelName = 'App\Models\Users';
    protected $route = "users";
    protected $fields = [
        "id" => [
            "label" => "ID",
            "hidden" => true
        ],
        "name" => [
            "label" => "Name",
            "sort" => true,
            "filter" => true
        ],
        "email" => [
            "label" => "Email",
            "sort" => true,
            "filter" => true
        ],
        "updated_at" => [
            "label" => "Last Update",
            "sort" => true
        ],
        "login_at" => [
            "label" => "Last Login"
        ],
        "user_type" => [
            "label" => "User Type",
            "hidden" => true
        ],
        "user_type_name" => [
            "field" => "user_types.name",
            "label" => "User Type",
            "sort" => true,
            "filter" => true
        ]
    ];

    protected function getQueryModel(){
        $query = parent::getQueryModel();
        $query->join('user_types','user_types.id=users.user_type');
        return $query;
    }
    
    public function index()
    {
        return $this->table("Users", $this->route);
    }

    function view($id){
        $item = $this->getModelById($id);
        return $this->parserLayout('users/view',['item'=>[$item],'title'=>'View User','editurl' => '/users/edit/'.$item['id']]);
    }

    private function getUserTypes(){
        $userTypes = new \App\Models\UserTypes();
        $result = [];
        foreach($userTypes->findAll() as $row){
            $result[$row["id"]]=$row["name"];
        }
        return $result;
    }

    function edit($id){
        $item = $this->getModelById($id);
        $item['password'] = '';
        return $this->layout('users/form',[
            'item'=>$item, 
            'userTypes'=> $this->getUserTypes(),
            'errors'=>$this->errors,
            'title'=>'Edit User']);
    }

    function prepareData($data){
        if (@$data["password"]){
            $data["password"] = md5($data["password"]);
        }
        return $data;
    }

    function update($id){
        $has_password = $this->request->getVar('password');
        $fields = ['name','email','user_type'];
        if ($has_password){
            $fields = array_merge($fields,['password','repeat_password']);
        }
        $rules = $this->model->getValidationRules(['only'=>$fields]);
        if ($has_password){
            $rules['repeat_password'] = 'matches[password]';
        }
        $result = $this->doUpdate($id, $fields,$rules);
        if (!$result){
            return $this->edit($id);
        }
        return $this->response->redirect('/users/edit/'.$id);
    }

    function new(){
        $item = [];
        return $this->layout('users/form',[
            'title' => 'Create User',
            'userTypes'=> $this->getUserTypes(),
            'item'=>$item
        ]);
    }

    function create(){
        $fields = ['name','email','user_type','password','repeat_password'];
        $rules = $this->model->getValidationRules(['only'=>$fields]);
        $rules['repeat_password'] = 'matches[password]';
        $id = $this->doCreate($fields,$rules);
        if (!$id){
            return $this->new();
        }
        return $this->response->redirect('/users/edit/'.$id);
    }

    function delete($id){
        $item = $this->getModelById($id);
        $this->model->delete($id);
        return redirect()->back();
    }

}
