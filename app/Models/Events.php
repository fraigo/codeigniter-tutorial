<?php

namespace App\Models;

use CodeIgniter\Model;

class Events extends BaseModel
{
    protected $DBGroup          = 'default';
    protected $table            = 'events';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'type',
        'value',
        'reference',
        'created_by'
    ];
    protected $relationships = [
        "users" => [
            "field"  => "created_by",
            "ext_id" => "id",
            "ext_description" => "name",
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
        'type' => [
            'label' => 'Type',
            'rules' => 'required|max_length[64]'
        ],
        'value' => [
            'label' => 'Value',
            'rules' => 'required|max_length[255]'
        ],
        'reference' => [
            'label' => 'Reference',
            'rules' => 'permit_empty'
        ],
        'created_by' => [
            'label' => 'Created By',
            'rules' => 'permit_empty'
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
