<?php

namespace App\Controllers;

class Notifications extends BaseController
{
    protected $modelName = 'App\Models\Notifications';
    protected $route = "notifications";
    protected $entityName = "Notification";
    protected $entityGroup = "Notifications";
    protected $viewFields = [];
    protected $editFields = ['title','content','icon','active','link'];
    public $fields = [
        "id" => [
            "label" => "Id",
            "hidden" => true,
        ],
        "title" => [
            "label" => "Title",
            "filter" => true,
        ],
        "icon" => [
            "label" => "Icon",
            "filter" => true,
        ],
        "active" => [
            "label" => "Active",
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
        "link" => [
            "label" => "Link",
            "filter" => true,
        ],
        "content" => [
            "header" => "Content",
            "component" => "html-editor",
            "view_component" => "html-editor",
            "label" => null,
            "hidden" => true,
        ],
        
    ];

    
    function getDetails($data){
        if (!@$data["item"]){
            return null;
        }
        if (!@$data["item"]["id"]){
            return null;
        }

        $_REQUEST["user_notifications_notification_id"] = $data["item"]['id'];

        $contents = [];
        $controller = new \App\Controllers\UserNotifications();
        $controller->initController($this->request, $this->response, $this->logger);
        $controller->prepareFields();
        $controller->fields['notification_id']["hidden"] = true;
        $controller->viewLink = "/usernotifications/view/{id}?notification_id={$data["item"]['id']}";
        $controller->newLink = "/usernotifications/new?notification_id={$data["item"]['id']}";
        $controller->editLink = "/usernotifications/edit/{id}?notification_id={$data["item"]['id']}";
        $contents[] = view('table',$controller->getTable("container mb-4"));

        return implode("\n",$contents);
    }

}