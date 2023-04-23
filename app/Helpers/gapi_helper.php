<?php

function gapi_token_file(){
    return $auth_file = ROOTPATH."/writable/gdrive.json";
}

function gapi_redirect_url($redirect=null){
    if (!$redirect){
        $redirect = str_replace("/index.php/","",$_SERVER['PHP_SELF']);
    }
    if (strpos($redirect,"http")===0){
        return $redirect;
    }
    return base_url() . $redirect;
}

function gapi_client($redirect,$scope=null,$offline=false){
    $client = new \Google\Client();
    $client->setAuthConfig(ROOTPATH.'/google.json');
    if ($scope) $client->addScope($scope);
    $redirect_uri = gapi_redirect_url($redirect);
    $client->setRedirectUri($redirect_uri);
    if ($offline) $client->setAccessType('offline');
    // Using "consent" ensures that your application always receives a refresh token.
    // If you are not using offline access, you can omit this.
    if ($offline) $client->setPrompt('consent');
    $client->setIncludeGrantedScopes(true);   // incremental auth
    return $client;
}

function glogin_client($redirect=null){
    return gapi_client($redirect,"https://www.googleapis.com/auth/userinfo.email");
}

function gdrive_client($redirect=null){
    $client = gapi_client($redirect,\Google\Service\Drive::DRIVE,true);
    return $client;
}

function gapi_save_token($client,$token){
    $token_file = gapi_token_file();
    file_put_contents($token_file,json_encode($token,JSON_PRETTY_PRINT));
    
}

function gapi_fetch_token($client,$code,$save=true){
    $token = $client->fetchAccessTokenWithAuthCode($code);
    if (@$token['access_token']){
        if ($save) {
            gapi_save_token($client, $token);
            $client->authenticate($code);
            return $client->getAccessToken();
        } else {
            return $token;
        }
    }
    return null;
}

function gapi_auth($client,$redirect=null){
    $access_token = null;
    if (isset($_GET['code'])) {
        gapi_fetch_token($client, $_GET['code']);
        $redirect_uri = gapi_redirect_url($redirect);
        header("Location: $redirect_uri");
        die();
    }
    if (file_exists(gapi_token_file())){
        $access_token = @json_decode(file_get_contents(gapi_token_file()),true);
    }
    if ($access_token){
        $client->setAccessToken($access_token);
    } else {
        return false;
    }
    if ($client->isAccessTokenExpired()){
        echo "Token has been renewed";
        $refreshToken = $client->getRefreshToken();
        $token = $client->refreshToken($refreshToken);
        if ($token['access_token']){
            gapi_save_token($client, $token);
            return true;
        }
        return false;
    }
    return true;
}

function gapi_userinfo($client){
    $oauth = new \Google\Service\Oauth2($client);
    return $oauth->userinfo->get();
}

function gdrive_file($client,$id,$fields='id,name,parents'){
    $drive = new \Google\Service\Drive($client);
    return $drive->files->get($id,['fields'=>$fields]);
}

function gdrive_files($client,$id="root",$onlyFolders=true){
    $drive = new \Google\Service\Drive($client);
    $file = null;
    $parent = null;
    $query = ["trashed=false"];
    if ($onlyFolders){
        $query[] = "mimeType='application/vnd.google-apps.folder'";
    }
    $file = $drive->files->get($id,['fields'=>'id,name,parents']);
    if ($file->parents && $file->parents[0]){
        $parent = $drive->files->get($file->parents[0],['fields'=>'id,name']);
    }
    $query[] = "'$id' in parents";
    $files = $drive->files->listFiles(['q'=>implode(' and ',$query)]);
    $items = $files->files  ?: [];
    return [
        "items" => $items,
        "parent" => $parent,
        "file" => $file,
    ];
}