<?php

function module_list(){
    return array_column(module_config(),"label","route");
}

function module_routes(){
    return array_column(module_config(),"controller","route");
}

function module_config(){
    return [
        [
            "route" => "users",
            "label" => "Users",
            "controller" => "Users"
        ],
        [
            "route" => "usertypes",
            "label" => "Profile Types",
            "controller" => "UserTypes"
        ],
        [
            "route" => "permissions",
            "label" => "Permissions",
            "controller" => "Permissions"
        ],
        [
            "route" => "lists",
            "label" => "Lists",
            "controller" => "Lists"
        ],
        [
            "route" => "listoptions",
            "label" => "List Options",
            "controller" => "ListOptions"
        ],
        [
            "route" => "notifications",
            "label" => "Notifications",
            "controller" => "Notifications"
        ],
        [
            "route" => "usernotifications",
            "label" => "User Notifications",
            "controller" => "UserNotifications"
        ],
        [
            "route" => "pages",
            "label" => "Pages",
            "controller" => "Pages"
        ],
        [
            "route" => "useroptions",
            "label" => "User Options",
            "controller" => "UserOptions"
        ],
    ];
}
