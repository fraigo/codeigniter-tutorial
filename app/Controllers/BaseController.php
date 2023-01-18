<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
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
abstract class BaseController extends Controller
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
    protected $helpers = ['html','array','auth'];

    protected $modelName = '';
    protected $entityName = 'User';
    protected $entityGroup = 'Users';
    protected $model = null;
    protected $viewFields = [];
    protected $editFields = [];
    public $fields = [];
    protected $errors = null;
    protected $route = '';

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

    protected function getModelById($id){
        $table = $this->model->table;
        $item = $this->getQueryModel()
            ->where("$table.id", $id)
            ->first();
        if (!$item){
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }
        return $item;
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

    protected function prepareFields($keys=null){
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
        $actions[] =  ["tag" => "a", "attributes" => [ 'class' => 'px-sm-1', 'href' => "/$route/view/{id}"], "content" => '👁'];
        if (module_access($route,2)){
            $actions[] = ["tag" => "a", "attributes" => [ 'class' => 'px-sm-1', 'href' => "/$route/edit/{id}"], "content" => '✏️'];
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
        foreach($this->fields as $fld=>$cfg){
            if (@$cfg["hidden"]){
                continue;
            }
            $indexCols[$fld] = $cfg;
            if (@$cfg["sort"]){
                $indexCols[$fld]["label"] = $this->sortColumn($fld,$indexCols[$fld]["label"],$group);
            }
        }
        return array_merge($actionCol,$indexCols);
    }

    protected function getTable($container=null)
    {
        $pagerGroup = $this->model->table;
        $pageSize = @$_GET["pagesize_$pagerGroup"]?:10;
        $query = $this->getQueryModel();
        $filters = $this->processFilters($query,$pagerGroup);
        $this->processSort($query,$pagerGroup);
        $items = $query->paginate($pageSize,$pagerGroup);
        $columns = $this->indexColumns($this->route,$pagerGroup);

        $data = [
            "title" => $this->entityGroup, // page $title
            "items" => $items,
            "route" => $this->route,
            "columns" => $columns,
            "filters" => $filters,
            "pager" => $this->model->pager,
            "pagesize" => $pageSize,
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
        return $data;
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

    public function index()
    {
        $this->prepareFields();
        return $this->layout('table',$this->getTable("container-lg"));
    }

    function view($id){
        $item = $this->getModelById($id);
        $fields = $this->prepareFields($this->viewFields);
        return $this->layout("view",[
            'route'=>$this->route,
            'item'=>$item,
            'fields'=>$fields,
            'title'=>"View $this->entityName",
            'editurl' => "/$this->route/edit/{$item['id']}"
        ]);
    }

    
    function edit($id){
        $item = $this->getModelById($id);
        $fields = $this->prepareFields($this->editFields);
        return $this->layout('form',[
            'item'=>$item, 
            'route'=> $this->route,
            'fields'=> $fields,
            'errors'=>$this->errors,
            'success' => session()->getFlashdata('success'),
            'title'=>"Edit $this->entityName"
        ]);
    }

    function update($id){
        $fields = $this->editFields;
        $rules = $this->getRules($fields);
        $result = $this->doUpdate($id,$fields,$rules);
        if (!$result){
            return $this->edit($id);
        }
        session()->setFlashData('success','Update successfull');
        return $this->replaceLocation("/$this->route/edit/$id");
    }

    function new(){
        $item = [];
        $fields = $this->prepareFields($this->editFields);
        return $this->layout('form',[
            'route' => $this->route,
            'fields' => $fields,
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
            return $this->new();
        }
        session()->setFlashData('success','Create successfull');
        return $this->replaceLocation("/$this->route/edit/$id");
    }

    function delete($id){
        $item = $this->getModelById($id);
        $this->model->delete($id);
        return redirect()->back();
    }

}
