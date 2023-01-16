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
    protected $fields = [
        'id' => [
            "label" => "ID",
            "hidden" => true
        ],
        'name' => [
            "label" => "Name",
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

}