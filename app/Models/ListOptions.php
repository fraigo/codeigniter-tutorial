<?php

namespace App\Models;

use CodeIgniter\Model;

class ListOptions extends BaseModel
{
    protected $DBGroup          = 'default';
    protected $table            = 'list_options';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'list_id',
        'name',
        'value'
    ];
    protected $relationships = [
        "lists" => [
            "field"  => "list_id",
            "ext_id" => "id",
            "ext_description" => "description",
        ]
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'list_id' => [
            'label' => 'List',
            'rules' => 'required|greater_than_equal_to[0]|unique_fields[list_options,list_id,name]'
        ],
        'name' => [
            'label' => 'Name',
            'rules' => 'required|max_length[64]|unique_fields[list_options,list_id,name]'
        ],
        'value' => [
            'label' => 'Value',
            'rules' => 'required|max_length[255]'
        ]
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function getOptionsByName($name){
        $listOptions = new \App\Models\ListOptions();
        $listOptions->select('list_options.value,list_options.name');
        $listOptions->join('lists','lists.id=list_options.list_id');
        $listOptions->where('lists.name',$name);
        return $listOptions->getListOptions('value','name');
    }

    public function getModel(){
        $listOptions = new \App\Models\ListOptions();
        $listOptions->join('lists','lists.id=list_options.list_id');
        $listOptions->select('list_options.*,lists.name as list_name');
        return $listOptions;
    }

}
