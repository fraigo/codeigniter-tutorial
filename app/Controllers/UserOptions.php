<?php

namespace App\Controllers;

class UserOptions extends BaseController
{
    protected $modelName = 'App\Models\UserOptions';
    protected $route = "useroptions";
    protected $entityName = "User Options";
    protected $entityGroup = "User Options";
    protected $viewFields = [];
    protected $editFields = ['user_id','option','value'];
    public $fields = [
        "id" => [
            "label" => "Id",
            "hidden" => true,
        ],
        "user_id" => [
            "label" => "User",
            "filter" => true,
        ],
        "option" => [
            "label" => "Option",
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
        $this->fields["user_id"]["options"] = $this->getListOptions('App\Models\Users','name');
        $this->fields["option"]["options"] = $this->model->getListUserOptions();
        return parent::prepareFields($keys);
    }

    
}