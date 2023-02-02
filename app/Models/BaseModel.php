<?php

namespace App\Models;

use CodeIgniter\Model;

class BaseModel extends Model
{
    // put here any common model method or attribute

    public function getListOptions($nameField, $id_field = "id", $condition=null){
        $result = [];
        if ($condition) $this->where($condition);
        foreach($this->findAll() as $row){
            $result[$row[$id_field]]=$row[$nameField];
        }
        return $result;
    }
}