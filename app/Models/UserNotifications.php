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

    public function createUserNotification($notificationId,$userId,$email=true){
        $users = new \App\Models\Users();
        $user = $users->find($userId);
        $notifications = new \App\Models\Notifications();
        $notif = $notifications->find($notificationId);
        if (!$user || !$notif){
            return -1;
        }
        $notification = new \App\Models\UserNotifications();
        $id = $notification->insert([
            "notification_id" => $notificationId,
            "user_id" => $userId,
            "read" => 0,
            "sent" => 0,
        ]);
        if ($email){
            try{
                helper("email");
                $baseURL = getenv("APP_URL") ?: base_url();
                $appURL = strpos($notif['link'],"http")===0 ? $notif['link'] : "$baseURL/{$notif['link']}";
                $result = send_email($user['email'], $notif['title'],"email/notification",["content"=>$notif['content'],"link"=>$appURL]);
                if ($result==null) $notification->update($id,[ "sent" => 1 ]);
            } catch (\Exception $e){
            }
        }
        if ($user['push_token']){
            helper('pushnotifications');
            $extra = ['link'=>$notif['link'],'notification'=>$notificationId,'usernotification'=>$id];
            $extra['payload'] = json_encode($extra);
            $txtMessage = str_replace('<br>',"\n",$notif['content']);
            $txtMessage = str_replace('<br />',"\n",$txtMessage); 
            $txtMessage = strip_tags($txtMessage);
            $result = @push_notification($user['push_token'],$notif['title'],$txtMessage,$extra);
        }
        return $id;
    }

}
