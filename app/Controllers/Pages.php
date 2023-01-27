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
            "component" => "html-editor",
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

    public function viewBySlug($slug=null){
        $item = $this->model->where("slug",$slug)->first();
        if (!$item){
            if ($this->isJson()){
                $this->JSONResponse(null,404,["message"=>"Not found"])->send();
                die();
            }
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }
        if (module_access('pages',2)){
            $item["editLink"] = $this->editLink ?: "/$this->route/edit/{$item['id']}";
        }
        return $this->layout('page',$item);
    }
}