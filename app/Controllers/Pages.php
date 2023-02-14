<?php

namespace App\Controllers;

class Pages extends BaseController
{
    protected $modelName = 'App\Models\Pages';
    protected $route = "pages";
    protected $entityName = "Page";
    protected $entityGroup = "Pages";
    protected $viewFields = [];
    protected $editFields = ['title','description','slug','contents'];
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
            "filter" => true,
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
            "label" => "Updated",
            "hidden" => true,
        ],
    ];

    public function view($id){
        $page = $this->getModelById($id);
        if (module_access('pages',2)){
            $page["editLink"] = $this->editLink ?: "/$this->route/edit/$id";
        }
        if ($this->isJson()) {
            $page["domcontent"] = parseHtml($page["contents"]);
            return $this->JSONResponse($page);
        }
        die("Ok");
        return $this->layout('page',$page);
    }

    public function viewBySlug($slug=null){
        $item = $this->model->where("slug",$slug)->first();
        if (!$item){
            $this->notFound();
        }
        if (module_access('pages',2)){
            $item["editLink"] = $this->editLink ?: "/$this->route/edit/{$item['id']}";
        }
        return $this->layout('page',$item);
    }
}