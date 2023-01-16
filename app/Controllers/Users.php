<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Users extends BaseController
{
    protected $modelName = 'App\Models\Users';
    protected $route = "users";
    protected $entityName = 'User';
    protected $entityGroup = 'Users';
    protected $viewFields = ['id','name','email','user_type','updated_at','login_at'];
    protected $editFields = ['name','email','user_type','password','repeat_password'];
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
        "user_type" => [
            "label" => "Profile",
            "filter" => true
        ],
        "password" => [
            "label" => "Password",
            "hidden" => true,
            "type" => "password",
            "field" => "",
        ],
        "repeat_password" => [
            "field" => "",
            "label" => "Repeat Password",
            "type" => "password",
            "hidden" => true,
        ],
        "updated_at" => [
            "label" => "Last Update",
            "sort" => true,
        ],
        "login_at" => [
            "label" => "Last Login"
        ]
    ];
    
    protected function getQueryModel(){
        $query = parent::getQueryModel();
        $query->join('user_types','user_types.id=users.user_type');
        return $query;
    }

    protected function prepareFields($keys=null){
        $this->fields["user_type"]["options"] = $this->getUserTypes();
        return parent::prepareFields($keys);
    }
    
    private function getUserTypes(){
        $userTypes = new \App\Models\UserTypes();
        $result = [];
        foreach($userTypes->findAll() as $row){
            $result[$row["id"]]=$row["name"];
        }
        return $result;
    }

    function getRules($fields){
        $rules = $this->model->getValidationRules(['only'=>$fields]);
        $rules['repeat_password'] = [
            "rules" => 'matches[password]',
            "label" => "Repeat password"
        ];
        return $rules;
    }

    function prepareData($data){
        if (@$data["password"]){
            $data["password"] = md5($data["password"]);
        }
        return $data;
    }

}
