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
        asort($result);
        return $result;
    }

    public function getRelationshipModel($extTable, $extFields=null, $leftJoin=false){
        $rel = @$this->relationships[$extTable];
        if ($rel){
            $field = $rel["field"];
            $idField = @$rel["ext_id"]?:'id';
            $desc = $rel["ext_description"];
            $extFields = $extFields?:["$desc as {$extTable}__$desc"];
            $modelTable = $this->table;
            $fields = array_merge(["$modelTable.*"],$extFields);
            $this->select($fields);
            $this->join($extTable, "$extTable.$idField=$modelTable.$field",$leftJoin?'left':'');
        }
    }

    protected function deleteChilds($data){
        foreach($this->childModels as $model=>$fields){
            $model = new $model();
            $flds = explode(",",$fields);
            foreach($flds as $field){
                $items = $model->select('id')->where($field,$data['id'])->findAll();
                foreach($items as $item){
                    $model->delete($item['id']);
                }
            }
        }
    }

    public function imageToURL($imageURL){
        if (strpos($imageURL,"data:")===0){
            list($type, $content) = explode(';', $imageURL);
            list($proto,$mime) = explode(':', $type);
            list($format, $rawdata)      = explode(',', $content,2);
            $contents = base64_decode($rawdata);
            $fileid = md5($contents);
            $extensions = [
                "image/png" => "png",
                "image/jpeg" => "jpg",
                "image/jpg" => "jpg",
            ];
            $ext = $extensions[$mime];
            $url = "/uploads/images/$fileid.$ext";
            $imagePath = ROOTPATH."writable/uploads/images";
            if (!file_exists($imagePath)){
                @mkdir($imagePath);
            }
            file_put_contents(ROOTPATH."writable$url",$contents);
            return $url;
        }
        return $imageURL;
    }
}