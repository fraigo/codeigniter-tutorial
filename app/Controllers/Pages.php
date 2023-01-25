<?php

namespace App\Controllers;

class Pages extends BaseController
{
    protected $modelName = 'App\Models\Pages';
    protected $route = "pages";
    protected $entityName = "Page";
    protected $entityGroup = "Pages";
    protected $viewFields = [];
    protected $editFields = ['title','description','contents','slug'];
    public $fields = [
        "id" => [
            "label" => "Id",
            "hidden" => true,
        ],
        "title" => [
            "label" => "Title",
            "filter" => true,
        ],
        "description" => [
            "label" => "Description",
            "filter" => true,
        ],
        "slug" => [
            "label" => "Slug",
        ],
        "contents" => [
            "header" => "Contents",
            "label" => null,
            "hidden" => true,
            "control" => "form_textarea",
        ],
        "created_at" => [
            "label" => "Created",
            "hidden" => true,
        ],
        "updated_at" => [
            "label" => "Updated_at",
            "hidden" => true,
        ],
    ];

    public function view($id){
        $page = $this->getModelById($id);
        if (module_access('pages',2)){
            $page["editLink"] = $this->editLink ?: "/$this->route/edit/$id";
        }
        return $this->layout('page',$page);
    }
}