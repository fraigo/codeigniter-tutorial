<?php

namespace App\Controllers;

class Events extends BaseController
{
    protected $modelName = 'App\Models\Events';
    protected $route = "events";
    protected $entityName = "Events";
    protected $entityGroup = "Events";
    protected $viewFields = [];
    protected $editFields = ['type','value','reference','created_by'];
    public $fields = [
        "id" => [
            "label" => "Id",
            "hidden" => true,
        ],
        "type" => [
            "label" => "Type",
            "maxlength" => "64",
            "filter" => true,
        ],
        "value" => [
            "label" => "Value",
            "maxlength" => "255",
            "filter" => true,
        ],
        "reference" => [
            "label" => "Reference",
            "type" => "number",
            "filter" => true,
        ],
        "created_by" => [
            "label" => "Created By",
            "type" => "number",
            "filter" => true,
        ],
        "created_at" => [
            "label" => "Created",
            "sort" => true,
        ],
        "updated_at" => [
            "label" => "Updated",
            "hidden" => true,
        ],
    ];

    protected function prepareFields($keys=null, $data=null){
        $this->fields["created_by"]["options"] = $this->getListOptions('App\Models\Users','name');
        return parent::prepareFields($keys);
    }

    
}