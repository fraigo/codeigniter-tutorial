<?php

namespace App\Controllers;

class UserNotifications extends BaseController
{
    protected $modelName = 'App\Models\UserNotifications';
    protected $route = "usernotifications";
    protected $entityName = "User Notifications";
    protected $entityGroup = "User Notifications";
    protected $viewFields = [];
    protected $editFields = ['notification_id','user_id','read','sent'];
    public $fields = [
        "id" => [
            "label" => "Id",
            "hidden" => true,
        ],
        "notification_id" => [
            "label" => "Notification",
            "filter" => true,
        ],
        "user_id" => [
            "label" => "User",
            "filter" => true,
        ],
        "read" => [
            "label" => "Read",
            "filter" => true,
            "options" => [
                "1" => "Yes",
                "0" => "No"
            ]
        ],
        "sent" => [
            "label" => "Sent",
            "filter" => true,
            "options" => [
                "1" => "Yes",
                "0" => "No"
            ]
        ],
        "created_at" => [
            "label" => "Created",
            "hidden" => true,
        ],
        "updated_at" => [
            "label" => "Updated",
            "hidden" => true,
        ],
    ];

    protected function prepareFields($keys=null, $data=null){
        $this->fields["notification_id"]["options"] = $this->getListOptions('App\Models\Notifications','title');
        $this->fields["user_id"]["options"] = $this->getListOptions('App\Models\Users','name');
        return parent::prepareFields($keys);
    }

    function userNotifications(){
        $userID = user_id();
        $notifications = $this->model;
        $notifications->getRelationshipModel("notifications",['title','content','link']);
        $notifications->where([
            'user_id' => $userID,
            'notifications.active' => 1
        ]);
        $notifications->where('user_notifications.created_at>=',date("Y-m-d",strtotime("-1 month")));
        $notifications->orderBy('user_notifications.created_at DESC');
        $items = $notifications->findAll();
        return $this->JSONResponse([
            "notifications" => $items,
        ]);
    }

    function updateUserNotification($id=null){
        $userNotifications = $this->model;
        $item = $userNotifications->find($id);
        if (!$item){
            return $this->notFound();
        }
        $read = $this->getVars("read");
        $result = $userNotifications->update($id,[
            "read" => $read ? 1 : 0
        ]);
        return $this->JSONResponse([
            "result" => $result,
        ]);
    }
    
}