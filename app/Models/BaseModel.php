<?php

namespace App\Models;

use App\Commands\Png2Jpg;
use CodeIgniter\Model;

class BaseModel extends Model
{
    // put here any common model method or attribute
    protected $relationships = [];
    protected $childModels = [];
    protected $imageFields = [];

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

    public function modelDetails($data){
        $details = [];
        foreach($this->childModels as $model=>$fields){
            $model = new $model();
            $model->getModel();
            $flds = explode(",",$fields);
            $details[$model->table] = [];
            foreach($flds as $field){
                $details[$model->table][$field] = $model->where($field,$data['id'])->findAll();
            }
        }
        return $details;
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

    public function imageToURL($imageURL,$target_extension=null){
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
            $fullPath = ROOTPATH."writable$url";
            $exists = file_exists($fullPath);
            file_put_contents($fullPath,$contents);
            if ($target_extension && $target_extension!=$ext){
                helper('image');
                $newUrl = rename_ext($url,$ext,$target_extension);
                png2jpg($fullPath,ROOTPATH."writable$newUrl");
                if (!$exists){
                    @unlink($fullPath);
                }
                return $newUrl;
            }
            return $url;
        }
        return $imageURL;
    }

    protected function imageConversion($data,$fields=null,$ext=null){
        $ext = $ext?:"jpg";
        $fields = $fields ?: $this->imageFields;
        foreach($fields as $fld){
            if (@$data["data"][$fld]){
                $data["data"][$fld] = $this->imageToURL($data["data"][$fld],$ext);
            }
        }
        return $data;
    }

    public function getModel(){
        return $this;
    }
}