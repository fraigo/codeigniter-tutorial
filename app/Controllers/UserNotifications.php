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

    function unreadNotifications(){
        $notifications = $this->userNotifications(null,true);
        $unread = array_filter($notifications, function($item) {
            return $item['read'] == 0 || $item['read'] == null;
        });
        $last_id = null;
        if (count($unread)){
            $last_id = $unread[0]['id'];
        }
        return $this->JSONResponse([
            "last_id" => $last_id,
            "total" => count($notifications),
            "unread" => count($unread),
        ]);
    }

    function userNotifications($date=null, $return=false){
        if (!$date) {
            $date = date("Y-m-d",strtotime("-1 month")) . " 00:00:00";
        }
        $userID = user_id();
        $notifications = $this->model;
        $notifications->getRelationshipModel("notifications",['title','content','link']);
        $notifications->where([
            'user_id' => $userID,
            'user_notifications.created_at>' => $date,
            'notifications.active' => 1
        ]);
        $notifications->orderBy('user_notifications.created_at DESC');
        $items = $notifications->findAll();
        if ($return) return $items;
        return $this->JSONResponse([
            "notifications" => $items,
            "from" => $date,
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