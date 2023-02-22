<?php

namespace App\Models;

use CodeIgniter\Model;

class BaseModel extends Model
{
    // put here any common model method or attribute
    protected $relationships = [];
    protected $childModels = [];

    public function getListOptions($nameFields, $id_field = "id", $condition=null){
        $result = [];
        if (!is_array($nameFields)) $nameFields = [$nameFields];
        if ($condition) $this->where($condition);
        foreach($this->findAll() as $row){
            $values = [];
            foreach($nameFields as $fld){
                if (array_key_exists($fld,$row)){
                    $values[] = $row[$fld];
                } else {
                    $values[] = $fld;
                }
            }
            $result[$row[$id_field]]=implode("",$values);
        }
        return $result;
    }

    public function getRelationshipModel($extTable, $extFields=null){
        $rel = @$this->relationships[$extTable];
        if ($rel){
            $field = $rel["field"];
            $idField = @$rel["ext_id"]?:'id';
            $desc = $rel["ext_description"];
            $extFields = $extFields?:["$desc as {$extTable}__$desc"];
            $modelTable = $this->table;
            $fields = array_merge(["$modelTable.*"],$extFields);
            $this->select($fields);
            $this->join($extTable, "$extTable.$idField=$modelTable.$field");
        }
    }

    protected function deleteChilds($data){
        foreach($this->childModels as $model=>$field){
            $model = new $model();
            $items = $model->select('id')->where($field,$data['id'])->findAll();
            foreach($items as $item){
                $model->delete($item['id']);
            }
        }
    }
}