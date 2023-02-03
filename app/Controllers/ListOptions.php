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

    protected function prepareFields($keys=null, $data=null){
        $this->fields["list_id"]["options"] = $this->getListOptions('App\Models\Lists','description');
        return parent::prepareFields($keys);
    }

    public function all(){
        $lists = new \App\Models\Lists();
        $allLists = $lists->findAll();
        $result = [];
        foreach($allLists as $list){
            $options = $this->model->where('list_id',$list['id'])->findAll();
            $items = array_column($options,"value","name");
            $result[$list['name']] = [
                "description" => $list["description"],
                "items" => $options,
                "keys" => $items
            ];
        }
        return $this->JSONResponse($result);
    }
}