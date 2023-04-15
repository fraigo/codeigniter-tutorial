<?php

namespace App\Controllers;

class Gapi extends BaseController
{
    var $client = null;
    protected $helpers = ['html','array','auth','module','gapi'];
    

    public function auth($type="drive",$client=null,$redirect='api/google/drive/browse'){
        if (@$_GET['redirect']){
            $redirect = $_GET['redirect'];
        }
        if (!$client){
            if ($type=="login"){
                $redirect = "api/google/login";
                if (@$_GET['redirect']){
                    $redirect = $_GET['redirect'];
                }
                $client = glogin_client($redirect);
            }
            if ($type=="drive"){
                $client = gdrive_client($redirect);
            }
        }
        $auth_url = $client->createAuthUrl();
        return $this->JSONResponse([
            "type" => $type,
            "url" => "$auth_url",
            "redirect" => gapi_redirect_url($redirect),
            "html" => "<a href=$auth_url style='font-family:Arial,sans;text-decoration:none;color:#444;padding: 12px 20px;border:1px solid #f0f0f0;border-radius: 7px;'><img src='/img/gapi/google.svg' align='absmiddle' width=24 height=24 > Sign In With Google</a>",
        ]);
    }

    public function token(){
        $redirect = @$_GET['redirect'] ?: "api/google/login";
        $client = glogin_client($redirect);
        if (@$_GET['code']){
            try{
                $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
                if (@$token['error']){
                    $error = "Invalid Authorization ({$token['error']})";
                    return $this->JSONResponse(null,400,["email"=>$error]);
                }
                $client->setAccessToken($token);
                $userinfo = gapi_userinfo($client);
                if (!$userinfo){
                    throw new \Exception("Google info not available");
                }
                $users = new \App\Models\Users();
                $user = $users->where('email',$userinfo['email'])->first();
                if ($user){
                    $login = do_login($user['id']);
                    if (!$login){
                        return $this->JSONResponse(null,400,["email"=>"The account is not available"]);
                    }
                    return $this->JSONResponse([
                        'user' => $login,
                        'userinfo' => $userinfo
                    ]);
                } else {
                    throw new \Exception('Email '.$userinfo['email'].' Not registered');
                }
            } catch(\Exception $e) {
                return $this->JSONResponse([
                    'redirect' => $redirect,
                    'code'=>$_GET['code'],
                    'token' => $token
                ],400,[
                    'email'=>$e->getMessage(),
                ]);
            }
            
        }
        return $this->JSONResponse(null,200,['email'=>'Invalid Authentication Token']);
    }

    public function browse($id="root"){
        $client = gdrive_client('api/google/drive/browse');
        $result = gapi_auth($client);
        if (!$result) {
            $this->auth("drive",$client);
        } else {
            $result = gdrive_files($client, $id, true);
            return view('gapi/browse',$result);
        }
    }

    public function select($id){
        $client = gdrive_client('gapi/drive/browse');
        $result = gapi_auth($client);
        if (!$result) {
            $this->auth("drive",$client);
        } else {
            $file = gdrive_file($client, $id);
            $name = ($file->name);
            echo "Selected folder '$name'";
            $path = ROOTPATH."/writable/folder.json";
            file_put_contents($path,json_encode($file));
        }
    }

}