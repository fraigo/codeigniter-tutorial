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
    protected $imageFields      = ['avatar_url'];
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
        'postal_code',
        'push_token'
    ];
    protected $relationships = [
        "user_types" => [
            "field"  => "user_type",
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
    protected $childModels = [
        '\App\Models\UserOptions' => 'user_id',
        '\App\Models\UserNotifications'=>'user_id',
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
            'rules' => 'required|max_length[32]|password_strength'
        ],
        'user_type' => [
            'label' => 'Profile',
            'rules' => 'required|greater_than_equal_to[0]'
        ],
        'avatar_url' => [
            'label' => 'Avatar',
            'rules' => 'permit_empty'
        ],
        'login_at' => [
            'label' => 'Last Login',
            'rules' => 'permit_empty'
        ],
        'password_token' => [
            'label' => 'Password Token',
            'rules' => 'permit_empty|max_length[64]'
        ],
        'password_token_expires' => [
            'label' => 'Password Token Exiration',
            'rules' => 'permit_empty'
        ],
        'auth_token' => [
            'label' => 'Auth Token',
            'rules' => 'permit_empty|max_length[128]'
        ],
        'phone' => [
            'label' => 'Phone',
            'rules' => 'permit_empty|max_length[64]'
        ],
        'address' => [
            'label' => 'Address',
            'rules' => 'permit_empty|max_length[255]'
        ],
        'city' => [
            'label' => 'City',
            'rules' => 'permit_empty|max_length[128]'
        ],
        'postal_code' => [
            'label' => 'Postal Code',
            'rules' => 'permit_empty|max_length[10]'
        ],
        'push_token' => [
            'label' => 'Push Token',
            'rules' => 'permit_empty|max_length[255]'
        ]
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['hashPassword','imageConversion'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['hashPassword','imageConversion'];
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
        $options = array_keys($userOptions->getListUserOptions());
        $opts = $userOptions->getUserOptions($id);
        foreach($opts as $key=>$value){
            if (!in_array($key,$options)){
                unset($opts[$key]);
            }
        }
        return $opts;
    }

    public function getUserValues($id){
        $userOptions = new \App\Models\UserOptions();
        $userOptions->createUserOptions($id);
        $options = array_keys($userOptions->getListUserOptions());
        $opts = $userOptions->getUserOptions($id);
        foreach($opts as $key=>$value){
            if (in_array($key,$options)){
                unset($opts[$key]);
            }
        }
        return $opts;
    }
    

    public function customCleanup($id){
        
    }
}
