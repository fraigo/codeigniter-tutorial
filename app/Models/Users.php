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
        'avatar_url',
        'login_at',
        'password_token',
        'password_token_expires',
        'auth_token',
        'phone',
        'address',
        'city',
        'postal_code'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    protected $childModels = [
        '\App\Models\UserOptions' => 'user_id',
    ];

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
            'label' => 'Profile',
            'rules' => 'required|greater_than_equal_to[0]'
        ],
        'password_token' => [
            'label' => 'Password Token',
            'rules' => 'max_length[64]'
        ],
        'auth_token' => [
            'label' => 'Auth Token',
            'rules' => 'max_length[128]'
        ],
        'phone' => [
            'label' => 'Phone',
            'rules' => 'max_length[64]'
        ],
        'address' => [
            'label' => 'Address',
            'rules' => 'max_length[255]'
        ],
        'city' => [
            'label' => 'City',
            'rules' => 'max_length[128]'
        ],
        'postal_code' => [
            'label' => 'Postal Code',
            'rules' => 'max_length[10]'
        ]
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['hashPassword'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['hashPassword'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = ['deleteChilds'];

    protected function hashPassword($data){
        if (@$data["data"]["password"]){
            @$data["data"]["password"] = md5($data["data"]["password"]);
        }
        return $data;
    }

    public function getUserOptions($id){
        $userOptions = new \App\Models\UserOptions();
        $userOptions->createUserOptions($id);
        return $userOptions->getUserOptions($id);
    }

}
