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

    public function loginEvent(){
        $loginData = [
            "ip"=>$_SERVER["REMOTE_ADDR"],
            "path"=>$_SERVER["PATH_INFO"]
        ];
        if (getenv('IPINFO_TOKEN')){
            $requestURI = "https://ipinfo.io/{$_SERVER["REMOTE_ADDR"]}?token=".getenv('IPINFO_TOKEN');
            $locationInfo = @file_get_contents($requestURI);
            if ($locationInfo){
                $locationData = json_decode($locationInfo, true);
                if ($locationData){
                    $loginData['loc'] = @$locationData['loc'];
                    $loginData['postal'] = @$locationData['postal'];
                    $loginData['timezone'] = @$locationData['timezone'];
                    $loginData['city'] = @$locationData['city'];
                    $loginData['country'] = @$locationData['country'];
                }
            }
        }
        return $this->addEvent('Login', json_encode($loginData), user_id());
    }

    public function addEvent($type, $value, $reference, $userId=null){
        $userId = $userId ?: user_id();
        $id = $this->insert([
            'type' => $type,
            'value' => $value,
            'reference' => $reference,
            'created_by' => $userId,
        ]);
        return $id;
    }

}
