<?php

namespace App\Controllers;

class Permissions extends BaseController
{
    protected $modelName = 'App\Models\Permissions';
    protected $route = "permissions";
    protected $entityName = "Permission";
    protected $entityGroup = "Permissions";
    protected $viewFields = [];
    protected $editFields = ['user_type_id','module','access'];
    protected $fields = [
        'id' => [
            "label" => "ID",
            "hidden" => true
        ],
        'user_type_id' => [
            "label" => "Profile",
            "sort" => true,
            "filter" => true
        ],
        'module' => [
            "label" => "Module",
            "sort" => true,
            "filter" => true
        ],
        "access" => [
            "label" => "Access",
            "sort" => true,
            "filter" => true,
            "options" => [
                1 => "View",
                2 => "Edit",
                3 => "Create",
                4 => "Full"
            ] 
        ]
    ];

    protected function prepareFields($keys=null){
        $this->fields["user_type_id"]["options"] = $this->getUserTypes();
        $this->fields["module"]["options"] = [
            "*" => "All",
            "users" => "Users",
            "usertypes" => "Profiles",
            "permissions" => "Permissions"
        ];
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

    private function getModules(){
        $modules = $this->model
            ->distinct()
            ->select("module as label, module as value")
            ->findAll();
        $result = [];
        foreach($modules as $row){
            $result[$row["value"]]=$row["label"];
        }
        return $result;
    }

}