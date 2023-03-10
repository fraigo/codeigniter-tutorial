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
    public $fields = [
        "id" => [
            "label" => "Id",
            "hidden" => true
        ],
        "user_type_id" => [
            "label" => "Profile",
            "sort" => true,
            "filter" => true
        ],
        "module" => [
            "label" => "Module",
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

    protected function prepareFields($keys=null, $data=null){
        $this->fields["user_type_id"]["options"] = $this->getListOptions('\App\Models\UserTypes','name');
        $this->fields["module"]["options"] = module_list();
        return parent::prepareFields($keys);
    }

}