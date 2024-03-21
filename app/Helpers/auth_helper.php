<?php

function create_token(){
    // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
    $data = $data ?? random_bytes(16);
    assert(strlen($data) == 16);

    // Set version to 0100
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    // Set bits 6-7 to 10
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    // Output the 36 character UUID.
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

function do_login($id, $event=false){
    $users = new \App\Models\Users();
    $users->select(['id','name','email','user_type','auth_token','push_token','login_at','phone','address','city','postal_code']);
    $user = $users->find($id);
    if ($user){
        $user["user_options"] = $users->getUserOptions($id);
    } else {
        return null;
    }
    
    $userTypes = new \App\Models\UserTypes();
    $userType = $userTypes
        ->select(['name','access'])
        ->find($user["user_type"]);
    
    $perm = new \App\Models\Permissions();
    $permissions = $perm->select(['module','access'])
        ->where("user_type_id",$user["user_type"])
        ->findAll();

    $authUsers = new \App\Models\AuthUsers();
    if (!$authUsers->isActive($user['id'])){
        return null;
    }
    
    $updatedData = [
        "login_at" => date("Y-m-d H:i:s"),
    ];
    if (!$user["auth_token"]){
        $updatedData["auth_token"] = create_token();
    }
    // if ($user['push_token']){
    //     helper('pushnotifications');
    //     $result = push_notification($user['push_token'],"Login","User has been logged in ",);
    // }
    $users->update($user["id"],$updatedData);
    $token = @$updatedData["auth_token"]?:$user["auth_token"];
    unset($user["auth_token"]);
    $result = [
        "user" => $user,
        "profile" => $userType,
        "permissions" => array_column($permissions,"access","module"),
        "token" => $token
    ];
    $session = session();
    foreach($result as $key=>$value){
        $session->set($key,$value);
    }
    if ($event){
        $events = new \App\Models\Events();
        $events->loginEvent();
    }
    return $result;
}

function clear_login(){
    $session = session();
    $session->set("user",'');
    $session->set("profile",'');
}

function check_login($request){
    if (@$_GET['__token__']){
        $users = new \App\Models\Users();
        $user = $users->where('auth_token',$_GET['__token__'])->first();
        if ($user){
            do_login($user['id']);
        }
    }
    if (@$_SERVER["HTTP_AUTHORIZATION"]){
        $user = null;
        list($type,$content) = explode(" ",$_SERVER["HTTP_AUTHORIZATION"]);
        $users = new \App\Models\Users();
        if ($type=="Bearer"){
            $user = $users->where('auth_token',$content)->first();
            
        }
        if ($type=="Basic"){
            $authInfo = base64_decode($content);
            if ($authInfo){
                @list($email,$password) = explode(":",$authInfo);
                $user = $users
                    ->where('email',$email)
                    ->where('password',md5($password))
                    ->first();
            }
        }
        if ($user){
            do_login($user['id']);
        }else{
            clear_login();
        }
    }
}

function check_user($user){
    $authUsers = new \App\Models\AuthUsers();
    return !$authUsers->isActive($user['id']);
}

function logged_in(){
    return session('user') ? true : false;
}

function current_user(){
    if (!logged_in()) return null;
    $user = session('user');
    $user['profile'] = session('profile');
    $user['permissions'] = session('permissions');
    return $user;
}

function user_id(){
    return @current_user()['id'];
}

function is_admin(){
    return @session('profile')['access']==4 ? true : false;
}

function module_access($module, $access){
    if (is_admin()) return true;
    $profileAccess = profile_access($module);
    if ($profileAccess>=$access){
        return true;
    }
    return false;
}

function profile_access($module){
    if (is_admin()) return 4;
    $profile = session('profile');
    $profileAccess = @$profile["access"]?:0;
    $permissions = session('permissions');
    if ($permissions){
        $profileAccess = @$permissions[$module]?:0;
    }
    return $profileAccess;
}


    