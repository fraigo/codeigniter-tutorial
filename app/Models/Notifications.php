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
    protected $childModels = [
        '\App\Models\UserNotifications'=>'notification_id',
    ];

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
    protected $afterDelete    = ['deleteChilds'];

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

    public function createUserNotification($notificationId,$user_ids=[],$template=null,$params=[]){
        $notifications =  new \App\Models\Notifications();
        if (!$notificationId){
            $notificationId = $notifications->insert([
                'title' => $title,
                'content' => $message,
                'icon' => null,
                'active' => 1,
                'link' => $link,
            ]);
        }
        $result =[];
        $userNotifications = new \App\Models\UserNotifications();
        $users = new \App\Models\Users();
        foreach($user_ids as $userId){
            $user = $users->find($userId);
            if ($user){
                $result[] = $userNotifications->createUserNotification($notificationId,$userId,true);
            }
        } 
        return $result;
    }

}
