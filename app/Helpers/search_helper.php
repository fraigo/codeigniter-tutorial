<?php

function search_config($type){
    $config = [
        "users" => [
            "controller" => '\App\Controllers\Users',
            "description" => "{name} ({email})",
        ],
        
    ];
    return @$config[$type];
}

function search_params($type){
    $config = search_config($type);
    if (!$config) return [];
    
    return $config;
}
