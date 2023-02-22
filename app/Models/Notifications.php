<?php

namespace App\Models;

use CodeIgniter\Model;

class Notifications extends BaseModel
{
    protected $DBGroup          = 'default';
    protected $table            = 'notifications';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'title',
        'content',
        'icon',
        'active',
        'link'
    ];
    protected $relationships = [
        
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'title' => [
            'label' => 'Title',
            'rules' => 'required|max_length[255]'
        ],
        'icon' => [
            'label' => 'Icon',
            'rules' => 'max_length[64]'
        ],
        'active' => [
            'label' => 'Active',
            'rules' => 'required|greater_than_equal_to[0]'
        ],
        'link' => [
            'label' => 'Link',
            'rules' => 'max_length[255]'
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

    public function createNotification($title,$content=null,$link=null){
        $notification = new \App\Models\Notifications();
        $id = $notification->insert([
            "title" => $title,
            "icon" => "",
            "content" => $content,
            "active" => 1,
            "link" => $link
        ]);
        return $id;
    }

}
