<?php

namespace App\Controllers;

class Lists extends BaseController
{
    protected $modelName = 'App\Models\Lists';
    protected $route = "lists";
    protected $entityName = "List";
    protected $entityGroup = "Lists";
    protected $viewFields = [];
    protected $editFields = ['name','description','active'];
    public $fields = [
        "id" => [
            "label" => "Id",
            "hidden" => true,
        ],
        "name" => [
            "label" => "Name",
            "filter" => true,
        ],
        "description" => [
            "label" => "Description",
            "filter" => true,
        ],
        "active" => [
            "label" => "Active",
            "filter" => true,
            "options" => [
                "1" => "Yes",
                "0" => "No"
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
        $controller = new \App\Controllers\ListOptions();
        $controller->initController($this->request, $this->response, $this->logger);
        $_REQUEST["list_options_list_id"] = $data["item"]['id'];
        $controller->prepareFields();
        $controller->fields['list_id']["hidden"] = true;
        $controller->viewLink = "/listoptions/view/{id}?list_id={$data["item"]['id']}";
        $controller->newLink = "/listoptions/new?list_id={$data["item"]['id']}";
        $controller->editLink = "/listoptions/edit/{id}?list_id={$data["item"]['id']}";
        return view('table',$controller->getTable(""));
    }
}