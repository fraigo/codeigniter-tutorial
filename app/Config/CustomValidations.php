<?php

namespace Config;

class CustomValidations {

    public function password_strength($password)
    {
        $hasNumbers = preg_match('#[0-9]#', $password);
        $hasLetters = preg_match('#[a-zA-Z]#', $password);
        if ($hasNumbers && $hasLetters) {
            return true;
        }
        return false;
    }

    public function unique_fields($value, $params, $data){
        @list($table,$field1,$field2,$idField) = explode(',',$params);
        $value1 = @$data[$field1];
        $value2 = @$data[$field2];
        $id = @$data[$idField?:"id"];
        $db = \Config\Database::connect("default");
        $result = $db->table($table)->where($field1,$value1)->where($field2,$value2)->get()->getResult();
        return $result ? $result[0]->id == $id : true;
    }

}