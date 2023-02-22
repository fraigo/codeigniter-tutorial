<?php

namespace App\Models;

use CodeIgniter\Model;

class UserNotifications extends BaseModel
{
    protected $DBGroup          = 'default';
    protected $table            = 'user_notifications';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'notification_id',
        'user_id',
        'read',
        'sent'
    ];
    protected $relationships = [
        "notifications" => [
            "field"  => "notification_id",
            "ext_id" => "id",
            "ext_description" => "title",
        ],
        "users" => [
            "field"  => "user_id",
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
        'notification_id' => [
            'label' => 'Notification',
            'rules' => 'required|greater_than_equal_to[0]|unique_fields[user_notifications,notification_id,user_id]'
        ],
        'user_id' => [
            'label' => 'User',
            'rules' => 'required|greater_than_equal_to[0]|unique_fields[user_notifications,notification_id,user_id]'
        ],
        'read' => [
            'label' => 'Read',
            'rules' => 'required|greater_than_equal_to[0]'
        ],
        'sent' => [
            'label' => 'Sent',
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

    public function createUserNotification($notificationId,$userId){
        $notification = new \App\Models\UserNotifications();
        $id = $notification->insert([
            "notification_id" => $notificationId,
            "user_id" => $userId,
            "read" => 0,
            "sent" => 0,
        ]);
        return $id;
    }

}
