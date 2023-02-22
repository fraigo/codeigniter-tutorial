<?php

namespace App\Models;

use CodeIgniter\Model;

class UserTypes extends BaseModel
{
    protected $DBGroup          = 'default';
    protected $table            = 'user_types';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'access'
    ];
    protected $childModels = [
        '\App\Models\Permissions'=>'user_type_id',
        '\App\Models\Users'=>'user_type',
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
            'rules' => 'required|max_length[255]|is_unique[user_types.name,id,{id}]'
        ],
        'access' => [
            'label' => 'Access',
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
    protected $afterDelete    = ['deleteChilds'];

}
