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

function gapi_client($redirect,$scopes=null,$offline=false,$select_account=true){
    $client = new \Google\Client();
    $client->setAuthConfig(ROOTPATH.'/google.json');
    if ($scopes){
        if (!is_array($scopes)) $scopes = [$scopes];
        foreach($scopes as $scope){
            $client->addScope($scope);
        }
    }
    $redirect_uri = gapi_redirect_url($redirect);
    $client->setRedirectUri($redirect_uri);
    if ($offline) $client->setAccessType('offline');
    // Using "consent" ensures that your application always receives a refresh token.
    // If you are not using offline access, you can omit this.
    $options = [];
    if ($select_account) $options[] = 'select_account';
    if ($offline) $options[] = 'consent';
    if (count($options)){
        $client->setPrompt(implode(' ',$options));
    }
    $client->setIncludeGrantedScopes(true);   // incremental auth
    return $client;
}

function glogin_client($redirect=null,$offline=false){
    $scopes = [
        "https://www.googleapis.com/auth/userinfo.email",
        "https://www.googleapis.com/auth/userinfo.profile",
    ];
    return gapi_client($redirect,$scopes,$offline);
}

function gdrive_client($redirect=null,$offline=true){
    $client = gapi_client($redirect,\Google\Service\Drive::DRIVE,$offline);
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
        return 0;
    }
    $renew = null;
    if ($client->isAccessTokenExpired()){
        $renew = true;
        $refreshToken = $client->getRefreshToken();
        $token = $client->refreshToken($refreshToken);
        if (@$token['access_token']){
            gapi_save_token($client, $token);
            return 2;
        }
        return 0;
    }
    return 1;
}

function gapi_userinfo($client){
    $oauth = new \Google\Service\Oauth2($client);
    return $oauth->userinfo->get();
}

function gdrive_file($client,$id,$fields='id,name,parents'){
    $drive = new \Google\Service\Drive($client);
    return $drive->files->get($id,['fields'=>$fields]);
}

function gdrive_file_upload($client,$drive_folder_id,$file,$filename=null){
    $drive = new \Google\Service\Drive($client);
    $filename = $filename ?: basename($file);
    $filetype = mime_content_type($file);
    $resource = new Google\Service\Drive\DriveFile([
        'name' => $filename,
        'parents' => [$drive_folder_id],
    ]);

    $result = $drive->files->create($resource, [
        'data' => file_get_contents($file),
        'mimeType' => $filetype,
        'uploadType' => 'multipart',
    ]);
    return $result;
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