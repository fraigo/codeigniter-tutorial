<?php

namespace App\Controllers;

class Search extends BaseController
{
    public function select($type=null)
    {
        $target = @$_GET["target"];
        helper('search');
        $config = search_params($type);
        if (!$config){
            return $this->notFound();
        }
        if (@$config["filters"])
        foreach($config["filters"] as $fld=>$val){
            $_REQUEST[$fld]=$val;
        }
        helper("form");
        $controllerName = $config["controller"];
        $controller = new $controllerName();
        $controller->initController($this->request,$this->response,$this->logger);
        $controller->prepareFields();
        if (@$config["filters"])
        foreach($config["filters"] as $fld=>$val){
           //$controller->fields["$fld"]["hidden"]=true;
        }
        $controller->actionColumn=[
            [
                "content"=>form_input([
                    "type"=>"button",
                    "value"=>"Select",
                    "data-description" => $config["description"],
                    "data-id" => "{id}",
                    "onclick"=>"selectItemFromSearch(this,'$target')"
                ])
            ]
        ];
        $controller->newLink = false;
        return $controller->layout('table',$controller->getTable("container-lg"),"search");
    }

    
}
