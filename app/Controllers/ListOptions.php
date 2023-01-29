<?php

namespace App\Controllers;

class ListOptions extends BaseController
{
    protected $modelName = 'App\Models\ListOptions';
    protected $route = "listoptions";
    protected $entityName = "List Option";
    protected $entityGroup = "List Options";
    protected $viewFields = [];
    protected $editFields = ['list_id','name','value'];
    public $fields = [
        "id" => [
            "label" => "Id",
            "hidden" => true,
        ],
        "list_id" => [
            "label" => "List",
            "filter" => true,
        ],
        "name" => [
            "label" => "Name",
            "filter" => true,
        ],
        "value" => [
            "label" => "Value",
            "filter" => true,
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

    protected function prepareFields($keys=null){
        $this->fields["list_id"]["options"] = $this->getListOptions('App\Models\Lists','description');
        return parent::prepareFields($keys);
    }
}