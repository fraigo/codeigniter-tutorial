<?php

function module_list(){
    $result = array_column(module_config(),"label","route");
    $result = array_merge($result,custom_modules());
    asort($result);
    return $result;
}

function module_menu(){
    $result = array_column(module_config(),"label","route");
    asort($result);
    return $result;
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
            "route" => "events",
            "label" => "Events",
            "controller" => "Events"
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

function custom_modules(){
    return [
        "auth_token" => "API Token",
        "gdrive" => "Google Drive access",
        "profile" => "Profile",
    ];
}
