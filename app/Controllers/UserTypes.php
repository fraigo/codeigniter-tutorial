<?php

namespace App\Controllers;

class UserTypes extends BaseController
{
    protected $modelName = 'App\Models\UserTypes';
    protected $route = "usertypes";
    protected $entityName = "Profile";
    protected $entityGroup = "Profiles";
    protected $viewFields = ['id','name','access'];
    protected $editFields = ['name','access'];
    public $fields = [
        "id" => [
            "label" => "Id",
            "hidden" => true,
        ],
        "name" => [
            "label" => "Name",
            "sort" => true,
            "filter" => true
        ],
        "access" => [
            "label" => "Access",
            "sort" => true,
            "filter" => true,
            "options" => [
                0 => "None",
                1 => "View",
                2 => "Edit",
                3 => "Create",
                4 => "Full"
            ] 
        ],
        "created_at" => [
            "label" => "Created",
            "hidden" => true,
        ],
        "updated_at" => [
            "label" => "Updated",
            "hidden" => true,
        ],
    ];

    function getDetails($data){
        if (!@$data["item"]){
            return null;
        }
        $_REQUEST["users_user_type"] = $data["item"]['id'];
        $_REQUEST["permissions_user_type_id"] = $data["item"]['id'];
        
        $controller = new \App\Controllers\Users();
        $controller->initController($this->request, $this->response, $this->logger);
        $controller->prepareFields();
        $controller->fields['user_type']["hidden"] = true;
        $controller->viewLink = "/users/view/{id}?user_type={$data["item"]['id']}";
        $controller->newLink = "/users/new?user_type={$data["item"]['id']}";
        $controller->editLink = "/users/edit/{id}?user_type={$data["item"]['id']}";
        $content[] = view('table',$controller->getTable(""));

        $controller = new \App\Controllers\Permissions();
        $controller->initController($this->request, $this->response, $this->logger);
        $controller->prepareFields();
        $controller->fields['user_type_id']["hidden"] = true;
        $controller->viewLink = "/permissions/view/{id}?user_type_id={$data["item"]['id']}";
        $controller->newLink = "/permissions/new?user_type_id={$data["item"]['id']}";
        $controller->editLink = "/permissions/edit/{id}?user_type_id={$data["item"]['id']}";
        $content[] = view('table',$controller->getTable(""));
        return implode("\n",$content);
    }

}