<?php

namespace App\Models;

use CodeIgniter\Model;

class Users extends BaseModel
{
    protected $DBGroup          = 'default';
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'email',
        'name',
        'password',
        'user_type',
        'login_at',
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'email' => [
            'label' => 'Email',
            'rules' => 'required|max_length[64]|valid_email|is_unique[users.email,id,{id}]'
        ],
        'name' => [
            'label' => 'Name',
            'rules' => 'required|max_length[128]'
        ],
        'password' => [
            'label' => 'Password',
            'rules' => 'required|max_length[32]'
        ],
        'user_type' => [
            'label' => 'User Type',
            'rules' => 'required|greater_than_equal_to[0]'
        ],
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
