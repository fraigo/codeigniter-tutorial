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
                "onchange" => "this.form.selected_fields.value+=this.value+'\\n'",
                "options" => array_combine($tableFields,$tableFields)
            ];
            $fields["selected_fields"] = [
                "label" => "Selected Fields",
                "control" => "form_textarea",
                "style" => "height: 240px; font-size: 16px;",
            ];
            $fields["default_values"] = [
                "label" => "Default Values",
                "control" => "form_textarea",
                "style" => "height: 200px; font-size: 16px;",
            ];
            $fields["file"] = [
                "label" => "CSV File",
                "type" => "file",
                "accept" => ".csv"
            ];
            $fields["content"] = [
                "label" => "CSV Content",
                "control" => "form_textarea",
            ];
        }
        return $this->layout('form',[
            'item'=>$item,
            'formAttributes'=>[
                "onsubmit"=>"tableChange(this.table.value);".($table?"":"return false"),
            ],
            'actionLabel' => 'Import',
            'action'=> current_url(),
            'fields'=> $fields,
            'errors'=>$this->errors,
            'success' => session()->getFlashdata('success'),
            'title'=>"Import Data"
        ]);
    }

    public function import($table="")
    {
        $list = explode("\n",$this->request->getVar("content"));
        $fields = explode("\n",$this->request->getVar("selected_fields"));
        $values = explode("\n",$this->request->getVar("default_values"));
        $results = [];
        $rows = [];
        $db = db_connect();
        foreach($list as $item){
            $cols = explode("\t",trim($item));
            $row = [];
            foreach($fields as $idx=>$fld){
                if ($fld==''){
                    break;
                }
                $row[$fld] = @$cols[$idx];
            }
            foreach($values as $idx=>$val){
                $parts = explode("=",trim($val));
                $row[$parts[0]] = $parts[1];
            }
            $row["created_at"] = date("Y-m-d H:i:s");
            $row["updated_at"] = date("Y-m-d H:i:s");
            try {
                $res = $db->table($table)->insert($row);
            } catch( \Exception $ex){
                $res = $ex->getMessage();
            }
            $rows[] = $row;
            $results[] = $res;
        }
        return $this->layout('import/results',[
            "results" => $results,
            "rows" => $rows
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
