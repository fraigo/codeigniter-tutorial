<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\I18n\Time;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends ResourceController
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = ['html','array','auth','module'];

    protected $modelName = '';
    protected $entityName = 'User';
    protected $entityGroup = 'Users';
    protected $model = null;
    protected $tableFields = null;
    protected $viewFields = [];
    protected $editFields = [];
    public $fields = [];
    protected $errors = null;
    protected $route = '';
    protected $formFilters = [];
    protected $pageSize = 10;
    protected $pageGroup = null;
    protected $editLink = null;
    protected $newLink = null;
    protected $viewLink = null;
    protected $formAttributes = null;

    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        $modelName = $this->modelName;
        if ($this->modelName){
            $this->model = new $modelName();
            $this->pageGroup = $this->model->table;
        }
    }

    protected function getQueryModel(){
        $item = $this->model
            ->select($this->selectFields());
        return $item;
    }

    protected function getDetails($data){
        return null;
    }

    protected function notFound(){
        if ($this->isJson()){
            $this->JSONResponse(null,404,["message"=>"Not found"])->send();
            die();
        }
        throw new \CodeIgniter\Exceptions\PageNotFoundException();
    }

    protected function getModelById($id){
        $table = $this->model->table;
        $item = $this->getQueryModel()
            ->where("$table.id", $id)
            ->first();
        if (!$item){
            $this->notFound();
        }
        return $item;
    }

    protected function getListOptions($model,$nameField,$idField="id",$condition=null){
        $items = new $model();
        return $items->getListOptions($nameField,$idField,$condition);
    }

    protected function layout($view, $data=[], $layout='default'){ 
        if (!@$data["details"]){
            $data["details"] = $this->getDetails($data);
        }
        return view($layout, [
            'content'=> view($view,$data)
        ]);
    }

    protected function parserView($view, $data=[]){ 
        $parser = \Config\Services::parser();
        return $parser->setData($data)->render($view);
    }

    protected function parserLayout($view, $data=[], $layout='default'){ 
        return view($layout, [
            'content'=> $this->parserView($view,$data)
        ]);
    }

    protected function prepareFields($keys=null, $data=null){
        $result = [];
        foreach($this->fields as $fld=>$cfg){
            if (!$keys || in_array($fld,$keys)){
                $result[$fld] = $cfg;
            }
        }
        return $result;
    }

    protected function selectFields(){
        $fields = [];
        foreach($this->fields as $fld=>$config){
            if (@$config["field"]===""){
                continue;
            }
            if (@$config["field"]){
                $fld = $config["field"]." as $fld";
            } else {
                $fld = $this->model->table.".".$fld;
            }
            $fields[] = $fld;
        }
        return $fields;
    }

    protected function processFilters($query, $group){
        $filters = [];
        foreach ($this->fields as $field => $config) {
            if (!@$config["filter"]){
                continue;
            }
            $filters[$field] = $config;
            if (@$config["field"]){
                $tableField = $config["field"];
            } else {
                $tableField = $this->model->table.".".$field;
            }
            $filterQuery = "{$group}_{$field}";
            $value = $this->request->getVar($filterQuery);
            $filters[$field]["value"] = $value;
            $filters[$field]["name"] = $filterQuery;
            if (@$config["options"]){
                $filters[$field]["control"] = "form_dropdown";
                $filters[$field]["options"] = ([""=>""])+$config["options"];
                $filters[$field]["selected"] = $value ? [$value] : [];
                if ($value){
                    $query = $query->where("$tableField",$value);
                }
            }
            else if ($value){
                $query = $query->like("$tableField",$value);
            }
        }
        $this->formFilters = $filters;
        return $filters;
    }


    protected function processSort($query,$group){
        $sortQuery = "sort_$group";
        $sort=$this->request->getVar($sortQuery);
        if ($sort) {
            @list($sortField,$sortDir) = explode(" ",$sort);
            $field = @$this->fields[$sortField]["field"];
            if ($field){
                $sortField = $field;
            } else {
                $sortField = $this->model->table.".".$sortField;
            }
            $query->orderBy("$sortField $sortDir");
            
        }
    }

    protected function sortColumn($field,$label,$group){
        $sortQuery = "sort_$group";
        $sortUrl = current_url(true);
        $sortUrl->stripQuery($sortQuery);
        $sort=$this->request->getVar($sortQuery);
        return anchor(
            $sortUrl->addQuery($sortQuery,$sort==$field?"$field desc":$field)->__toString(),
            $label.($sort==$field?' ↓':($sort=="$field desc"?' ↑':''))
        );
    }

    protected function actionColumns($route){
        $actions = [];
        $editLink = $this->editLink ?: "/$route/edit/{id}";
        $viewLink = $this->viewLink ?: "/$route/view/{id}";
        $actions[] =  ["tag" => "a", "attributes" => [ 'class' => 'px-sm-1', 'href' => $viewLink], "content" => '👁'];
        if (module_access($route,2)){
            $actions[] = ["tag" => "a", "attributes" => [ 'class' => 'px-sm-1', 'href' => $editLink], "content" => '✏️'];
        }
        if (module_access($route,4)){
            $actions[] = ["tag" => "a", "attributes" => [ 
                'class' => 'px-sm-1', 
                'href' => "/$route/delete/{id}",
                'onclick' => "return confirm('Are you sure you want to delete this item?')"
            ], "content" => '🗑'];
        };
        return $actions;
    }

    protected function indexColumns($route, $group){
        $actionCol = [];
        if ($route){
            $actionCol = [[
                "content" => $this->actionColumns($route),
                "cellAttributes" => [
                    "class" => "actions text-center text-nowrap",
                    "width" => "100"
                ]
            ]];
        }
        $indexCols = [];
        if (!$this->tableFields){
            $this->tableFields=[];
            foreach($this->fields as $fld=>$cfg){
                if (!@$cfg["hidden"]){
                    $this->tableFields[]=$fld;
                }
            }
        }
        foreach($this->tableFields as $fld){
            $cfg = $this->fields[$fld];
            $indexCols[$fld] = $cfg;
            if (@$cfg["sort"]){
                $indexCols[$fld]["label"] = $this->sortColumn($fld,$indexCols[$fld]["label"],$group);
            }
        }
        return array_merge($actionCol,$indexCols);
    }

    function isJson(){
        $url = current_url(true);
        return $url->getSegment(1) == 'api' || strpos($this->request->getHeaderLine('Content-Type'), 'application/json') !== false;
    }

    protected function getAction(){
        $url = current_url(true);
        if ($this->isJson()) {
            $method = $_SERVER['REQUEST_METHOD'];
            if (in_array($method,['POST', 'PUT'])){
                return "edit";
            }
            return "";
        } else {
            return $url->getSegment(2);
        }
    }

    protected function getItems(){
        $pagerGroup = $this->pageGroup;
        $pagerQuery = $pagerGroup == 'default' ? 'pagesize' : "pagesize_$pagerGroup";
        $this->pageSize = @$_GET["$pagerQuery"]?:$this->pageSize;
        $query = $this->getQueryModel();
        $this->processFilters($query,$pagerGroup);
        $this->processSort($query,$pagerGroup);
        $items = $query->paginate($this->pageSize,$pagerGroup);
        return $items;
    }

    protected function getTable($container=null)
    {
        $pagerGroup = $this->model->table;
        $items = $this->getItems();
        $filters = $this->formFilters;
        $columns = $this->indexColumns($this->route,$pagerGroup);
        $this->newLink = $this->newLink ?: "/$this->route/new";

        $data = [
            "title" => $this->entityGroup, // page $title
            "items" => $items,
            "route" => $this->route,
            "newUrl" => $this->newLink,
            "columns" => $columns,
            "filters" => $filters,
            "pager" => $this->model->pager,
            "pagesize" => $this->pageSize,
            "pager_group" => $pagerGroup,
            "container" => $container,
        ];

        return $data;
    }

    function prepareData($data){
        return $data;
    }

    function doUpdate($id, $fields, $rules=null){
        $item = $this->getModelById($id);
        $data = $this->request->getVar($fields);
        $data["id"] = $id;
        $rules = $rules ?: $this->model->getValidationRules(['only'=>$fields]);
        $validation = \Config\Services::validation();
        $validation->setRules($rules);
        if (!$validation->run($data)){
            $this->errors = $validation->getErrors();
            return null;
        }
        $data = $this->prepareData($data);
        $result = $this->model->update($item["id"],$data);
        return $result;
    }

    function doCreate($fields, $rules=null){
        $data = $this->request->getVar($fields);
        $rules = $this->model->getValidationRules(['only'=>$fields]);
        $validation = \Config\Services::validation();
        $validation->setRules($rules);
        if (!$validation->run($data)){
            $this->errors = $validation->getErrors();
            return false;
        }
        $data = $this->prepareData($data);
        $id = $this->model->insert($data);
        return $id;
    }

    protected function replaceLocation($location){
        $loc = json_encode($location);
        return "<script>document.location.replace($loc);</script>";
    }

    function getRules($fields){
        $rules = $this->model->getValidationRules(['only'=>$fields]);
        return $rules;
    }

    protected function JSONResponse($data, $status=200, $errors=null){
        $response = [
            "success" => ($errors === null),
            "date" => date("Y-m-d H:i:s"),
        ];
        if ($errors){
            $response["errors"] = $errors;
        } else {
            $response["data"] = $data;
        }
        return $this->response->setStatusCode($status)->setJSON($response);
    }

    public function index()
    {
        $this->prepareFields();
        if ($this->isJson()){
            $this->pageSize = 100;
            $this->pageGroup = 'default';
            $items = $this->getItems();
            $page = $this->model->pager->getCurrentPage();
            $pages = $this->model->pager->getPageCount();
            $next = current_url(true)->addQuery("page",$page+1)->__toString();
            $result = [
                "items" => $items,
                "fields" => $this->fields,
                "page" => $page,
                "pages" => $pages,
                "next" => $pages>$page ? $next : null,
                "pagesize" => $this->pageSize,
            ];
            
            return $this->JSONResponse($result);
        }
        return $this->layout('table',$this->getTable("container-lg"));
    }

    public function view($id){
        $item = $this->getModelById($id);
        $fields = $this->prepareFields($this->viewFields,$item);
        if ($this->isJson()){
            return $this->JSONResponse($item);
        }
        $editLink = $this->editLink ?: "/$this->route/edit/{$item['id']}";
        $extra = [];
        foreach($this->editFields as $fld){
            if (@$_GET[$fld]){
                $extra[] = "$fld={$_GET[$fld]}";
            }
        }
        if (count($extra) > 0){
            $editLink .= "?".implode('&',$extra);
        }
        if (!module_access($this->route,2)){
            $editLink = null;
        }
        return $this->layout("view",[
            'editLink'=>$editLink,
            'backLink'=>"/$this->route",
            'item'=>$item,
            'fields'=>$fields,
            'title'=>"View $this->entityName",
            'editurl' => "/$this->route/edit/{$item['id']}"
        ]);
    }

    
    public function edit($id=null){
        $item = $this->getModelById($id);
        $fields = $this->prepareFields($this->editFields,$item);
        foreach($this->editFields as $fld){
            if (@$_GET[$fld]){
                $fields[$fld]["readonly"] = true;
            }
        }
        return $this->layout('form',[
            'item'=>$item,
            'formAttributes'=>$this->formAttributes,
            'action'=> current_url(),
            'fields'=> $fields,
            'errors'=>$this->errors,
            'success' => session()->getFlashdata('success'),
            'title'=>"Edit $this->entityName"
        ]);
    }

    function update($id=null){
        $fields = $this->editFields;
        if ($this->isJSON()){
            $jsonData = $this->request->getJSON(true);
            if (!is_array($jsonData)){
                return $this->JSONResponse(null,400,[
                    "message"=>"Invalid request"
                ]);
            }
            $fields = array_intersect($fields,array_keys($jsonData));
        }
        $rules = $this->getRules($fields);
        $result = $this->doUpdate($id,$fields,$rules);
        if (!$result){
            if ($this->isJson()){
                return $this->JSONResponse(null,400,$this->errors);
            }
            return $this->edit($id);
        }
        if ($this->isJson()){
            return $this->JSONResponse($this->getModelById($id));
        }
        session()->setFlashData('success','Update successfull');
        return $this->replaceLocation(current_url());
    }

    function new(){
        $item = [];
        $fields = $this->prepareFields($this->editFields,$item);
        foreach($this->editFields as $fld){
            if (@$_GET[$fld]){
                $item[$fld]=$_GET[$fld];
                $fields[$fld]["readonly"] = true;
            }
        }
        return $this->layout('form',[
            'action' => "/$this->route/new",
            'fields' => $fields,
            'formAttributes'=>$this->formAttributes,
            'title' => "Create $this->entityName",
            'success' => session()->getFlashdata('success'),
            'errors' => $this->errors,
            'item'=>$item
        ]);
    }

    function create(){
        $fields = $this->editFields;
        $rules = $this->getRules($fields);
        $id = $this->doCreate($fields,$rules);
        if (!$id){
            if ($this->isJson()){
                return $this->JSONResponse(null,400,$this->errors);
            }
            return $this->new();
        }
        if ($this->isJson()){
            return $this->JSONResponse($this->getModelById($id));
        }
        session()->setFlashData('success','Create successfull');
        return $this->replaceLocation("/$this->route/edit/$id");
    }

    function delete($id=null){
        $item = $this->getModelById($id);
        $result = $this->model->delete($id);
        if ($this->isJson()){
            return $this->JSONResponse($item);
        }
        return redirect()->back();
    }

}
