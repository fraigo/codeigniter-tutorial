<?php

function is_admin(){
    return session('admin') ? true : false;
}

function module_access($module, $access){
    if (session('admin')) return true;
    $profileAccess = profile_access($module);
    if ($profileAccess>=$access){
        return true;
    }
    return false;
}

function profile_access($module){
    if (session('admin')) return 4;
    $profile = session('profile');
    $profileAccess = $profile["access"];
    $permissions = session('permissions');
    if ($permissions){
        $profileAccess = @$permissions[$module]?:0;
    }
    return $profileAccess;
}


    