<?php

namespace App\Models;

use CodeIgniter\Model;

class Lists extends BaseModel
{
    protected $DBGroup          = 'default';
    protected $table            = 'lists';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'description',
        'active'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'name' => [
            'label' => 'Name',
            'rules' => 'required|max_length[64]|is_unique[lists.name,id,{id}]'
        ],
        'description' => [
            'label' => 'Description',
            'rules' => 'required|max_length[255]'
        ],
        'active' => [
            'label' => 'Active',
            'rules' => 'required|greater_than_equal_to[0]'
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

}
