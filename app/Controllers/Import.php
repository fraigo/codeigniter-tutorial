<?php

namespace App\Controllers;

use \CodeIgniter\Database\Exceptions\DatabaseException;

class Import extends BaseController
{
    public function index($table="")
    {
        $item=[
            "table" => $table,
            "operation" => "insert",
            "truncate" => "no",
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
            $fields["operation"] = [
                "label" => "Operation",
                "options" => [
                    "insert" => "Insert",
                    "replace" => "Replace",
                ],
            ];
            $fields["truncate"] = [
                "label" => "Truncate",
                "options" => [
                    "no" => "Leave Existing Data",
                    "yes" => "Clear Data",
                    
                ],
            ];
            $fields["file"] = [
                "label" => "CSV File",
                "type" => "file",
                "onchange" => "parseFile(this)",
                "accept" => ".csv"
            ];
            $fields["content"] = [
                "label" => "CSV Content",
                "placeholder" => "Data separated by comma or tab space",
                "control" => "form_textarea",
                "onchange" => "parseHeaders(this)"
            ];
            $fields["fields"] = [
                "label" => "Fields",
                "onchange" => "this.form.selected_fields.value+=this.value+'\\n'",
                "options" => array_combine($tableFields,$tableFields),
                "default_option" => "Select Field",
            ];
            $fields["selected_fields"] = [
                "label" => "Selected Fields",
                "placeholder" => "Pick fields from selector",
                "control" => "form_textarea",
                "style" => "height: 180px; font-size: 16px;",
            ];
            $fields["default_values"] = [
                "label" => "Default Values",
                "placeholder" => "field=value",
                "control" => "form_textarea",
                "style" => "height: 120px; font-size: 16px;",
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
        $replace = "replace" == $this->request->getVar("operation");
        $truncate = "yes" == $this->request->getVar("truncate");
        $fields = explode("\n",$this->request->getVar("selected_fields"));
        $values = explode("\n",$this->request->getVar("default_values"));
        $rows = [];
        $db = db_connect();
        if ($truncate){
            $db->table($table)->truncate(); 
        }
        
        foreach($list as $item){
            $cols = explode("\t",trim($item));
            foreach($fields as $idx=>$fld){
                if (trim($fld)==''){
                    break;
                }
                $row[$fld] = @$cols[$idx];
            }
            foreach($values as $idx=>$val){
                if (strpos($val,'=')===false){
                    break;
                }
                $parts = explode("=",trim($val));
                $row[$parts[0]] = $parts[1];
            }
            $row["created_at"] = date("Y-m-d H:i:s");
            $row["updated_at"] = date("Y-m-d H:i:s");
            try {
                if ($replace){
                    $res = $db->table($table)->replace($row);
                } else {
                    $res = $db->table($table)->insert($row);
                }
            } catch( DatabaseException $ex){
                $res = $ex->getMessage();
            }
            $rows[] = array_merge(["RESULT" => $res],$row);
        }
        return $this->layout('table',[
            "title" => "Results",
            "items" => $rows,
            "route" => '',
            "newUrl" => '/import',
            "columns" => null,
            "filters" => [],
            "container" => '',
        ]);
    }

    public function getDetails($data){
        return view('import/scripts');
    }

    private function getTables(){
        $db = db_connect();
        $tables = $db->listTables();
        sort($tables);
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
