<?php

namespace App\Controllers;

class Import extends BaseController
{
    public function index($table="")
    {
        $item=[
            "table" => $table
        ];
        $fields = [
            "table" => [
                "label" => "Table",
                "options" => $this->getTables(),
                "onchange" => "tableChange(this.value)",
            ],
        ];
        if ($table){
            $tableFields = $this->getFields($table);
            $height = (count($tableFields)+1) * 1.25;
            $fields["fields"] = [
                "label" => "Fields",
                "multiple" => true,
                "style" => "height: {$height}em; font-size: 16px;",
                "options" => $tableFields
            ];
            $fields["file"] = [
                "label" => "Fields",
                "type" => "file",
                "accept" => ".csv"
            ];
        }
        return $this->layout('form',[
            'item'=>$item,
            'formAttributes'=>[
                "onsubmit"=>"tableChange(this.table.value);return false",
            ],
            'action'=> current_url(),
            'fields'=> $fields,
            'errors'=>$this->errors,
            'success' => session()->getFlashdata('success'),
            'title'=>"Import Data"
        ]);
    }

    public function getDetails($data){
        return view('import/scripts');
    }

    private function getTables(){
        $db = db_connect();
        $tables = $db->listTables();
        $result = [];
        foreach ($tables as $table) {
            $result[$table] = ($table);
        }
        return $result;
    }

    private function getFields($table=null){
        $db = db_connect();
        $tables = $this->getTables();
        if ($table && @$tables[$table]) return $db->getFieldNames($table);
        foreach ($tables as $table=>$name){
            $tables[$table] = $db->getFieldNames($table);
        }
        return $tables;
    }
}
