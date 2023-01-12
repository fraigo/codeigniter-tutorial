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
    protected $helpers = ['html','array'];

    protected $modelName = '';
    protected $model = null;
    protected $fields = [];
    protected $errors = null;

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

    protected function getModelById($id){
        $item = $this->model
            ->where("id", $id)
            ->select($this->selectFields())
            ->first();
        if (!$item){
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }
        return $item;
    }

    protected function layout($view, $data=[], $layout='default'){ 
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

    protected function selectFields(){
        $fields = [];
        foreach($this->fields as $fld=>$config){
            $fields[] = $fld;
        }
        return $fields;
    }

    protected function processFilters($query, $filterFields, $group){
        $filters = array_filter_by_keys($this->fields,$filterFields);
        foreach ($filters as $field => $config) {
            $tableField = $field;
            $filterQuery = "{$group}_{$field}";
            $value = $this->request->getVar($filterQuery);
            if ($value){
                $query = $query->like("$tableField",$value);
            }
            $filters[$field]["value"] = $value;
            $filters[$field]["name"] = $filterQuery;
        }
        return $filters;
    }


    protected function processSort($query,$group){
        $sortQuery = "sort_$group";
        $sort=$this->request->getVar($sortQuery);
        if ($sort) {
            @list($sortField,$sortDir) = explode(" ",$sort);
            $field = @$this->fields[$sortField]["field"];
            $query->orderBy("$field $sortDir");
            
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

    protected function actionColumns($url){
        return [
            ["tag" => "a", "attributes" => [ 'class' => 'px-sm-1', 'href' => "$url/view/{id}"], "content" => '👁'],
            ["tag" => "a", "attributes" => [ 'class' => 'px-sm-1', 'href' => "$url/edit/{id}"], "content" => '✏️'],
            ["tag" => "a", "attributes" => [ 
                'class' => 'px-sm-1', 
                'href' => "$url/delete/{id}",
                'onclick' => "return confirm('Are you sure you want to delete this item?')"
            ], "content" => '🗑'],
        ];
    }

    protected function indexColumns($fields,$actionUrl, $group){
        $actionCol = [];
        if ($actionUrl){
            $actionCol = [[
                "content" => $this->actionColumns($actionUrl),
                "cellAttributes" => [
                    "class" => "actions text-center text-nowrap",
                    "width" => "100"
                ]
            ]];
        }
        $indexCols = array_filter_by_keys($this->fields,$fields);
        foreach($indexCols as $fld=>$cfg){
            if (@$cfg["sort"]){
                $indexCols[$fld]["label"] = $this->sortColumn($fld,$indexCols[$fld]["label"],$group);
            }
        }
        return array_merge($actionCol,$indexCols);
    }
}
