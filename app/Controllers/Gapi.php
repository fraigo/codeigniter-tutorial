<?php

namespace App\Controllers;
use CodeIgniter\I18n\Time;

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
        $html = "<a href=$auth_url style='font-family:Arial,sans;text-decoration:none;color:#444;padding: 12px 20px;border:1px solid #f0f0f0;border-radius: 7px;'><img src='/img/gapi/google.svg' align='absmiddle' width=24 height=24 > Sign In With Google</a>";
        if(@$_GET["format"]=="html"){
            die($html);
        }
        return $this->JSONResponse([
            "type" => $type,
            "url" => "$auth_url",
            "redirect" => gapi_redirect_url($redirect),
            "html" => $html,
        ]);
    }

    public function token($app=false){
        $redirect = @$_GET['redirect'] ?: "api/google/login";
        if ($app) $redirect = "https://{$_SERVER['HTTP_HOST']}".explode('?',$_SERVER['REQUEST_URI'])[0];
        $client = glogin_client($redirect);
        $BASEURL = getenv('APP_URL')."/";
        $deviceid = @$_GET['deviceid'] ?: '';
        if (@$_GET['code']){
            try{
                $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
                if (@$token['error']){
                    $error = "Invalid Authorization ({$token['error']})";
                    return $this->JSONResponse(null,400,["email"=>$error,"redirect"=>$redirect]);
                }
                $client->setAccessToken($token);
                $userinfo = gapi_userinfo($client);
                if (!$userinfo){
                    throw new \Exception("Google info not available");
                }
                $users = new \App\Models\Users();
                $user = $users->where('email',$userinfo['email'])->first();
                if ($user){
                    $login = do_login($user['id'],true,$deviceid);
                    if ($app){
                        if (!$login){
                            return $this->layout('auth/error',[
                                'title'=>'Error',
                                'message'=>lang('App.account_unavailable'),
                                'url'=>$BASEURL
                            ],'login');
                        }
                        $tokenid = substr($token['access_token'],0,64);
                        $users->update($user['id'],[
                            'login_at' => date("Y-m-d H:i:s"),
                            'password_token' => $tokenid,
                            'password_token_expires' => Time::parse('+1 hours')->toDateTimeString()
                        ]);
                        $url = getenv('APP_URL')."/";
                        return $this->layout('auth/google',['url'=>$url,'token'=>$tokenid],'login');
                    }
                    if (!$login){
                        return $this->JSONResponse(null,400,["email"=>lang('App.account_unavailable')]);
                    }
                    return $this->JSONResponse([
                        'user' => $login,
                        'userinfo' => $userinfo
                    ]);
                } else {
                    if (getenv("REGISTER_USER")=="true"){
                        $passwd = "p".rand(1000000,99999999);
                        // $userinfo["givenName"] $userinfo["familyName"] 
                        $result = $users->insert([
                            "name" => @$userinfo['name'] ?: explode('@',$userinfo['email'])[0],
                            "email" => $userinfo['email'],
                            "user_type" => getenv('REGISTER_USER_TYPE')?:1,
                            "password" => $passwd,
                            "repeat_password" => $passwd,
                        ]);
                        $user = $users->where('email',$userinfo['email'])->first();
                        if ($user) {    
                            if ($app){
                                $tokenid = substr($token['access_token'],0,64);
                                $users->update($user['id'],[
                                    'login_at' => date("Y-m-d H:i:s"),
                                    'password_token' => $tokenid,
                                    'password_token_expires' => Time::parse('+1 hours')->toDateTimeString()
                                ]);
                                $url = getenv('APP_URL')."/";
                                return $this->layout('auth/google',['url'=>$url,'token'=>$tokenid],'login');    
                            }
                            $login = do_login($user['id'],true,$deviceid);
                            return $this->JSONResponse([
                                'user' => $login,
                                'userinfo' => $userinfo
                            ]);    
                        }
                    }
                    if ($app){
                        return $this->layout('auth/notfound',[
                            'email'=>$userinfo['email'],
                            'url'=>$BASEURL
                        ],'login');
                    }
                    throw new \Exception('Email '.$userinfo['email'].' Not registered');
                }
            } catch(\Exception $e) {
                return $this->JSONResponse([
                    'redirect' => $redirect,
                    'code'=>$_GET['code'],
                    'token' => $token,
                    'line'=>$e->getLine(),
                ],400,[
                    'email'=>$e->getMessage(),
                ]);
            }
            
        }
        return $this->JSONResponse(null,200,['email'=>'Invalid Authentication Token']);
    }

    public function browse($id="root"){
        $client = gdrive_client('api/google/drive/browse');
        $authResult = gapi_auth($client);
        if (!$authResult) {
            $this->auth("drive",$client);
        } else {
            $result = gdrive_files($client, $id, true);
            $result['selected'] = null;
            $result['auth'] = $authResult==2 ? 'renew' : 'ok';
            $path = ROOTPATH."/writable/folder.json";
            if (file_exists($path)){
                $result['selected'] =  json_decode(file_get_contents($path), true);
            }
            if(@$_GET["format"]=="html"){
                return view('gapi/browse',$result);
            }
            
            return $this->JSONResponse($result);
        }
    }

    public function select($id){
        $client = gdrive_client('gapi/drive/browse');
        $result = gapi_auth($client);
        if (!$result) {
            $this->auth("drive",$client);
        } else {
            $file = @gdrive_file($client, $id);
            $name = $file->name;
            if (!$file){
                return $this->JSONResponse(null,400,['id'=>'Invalid File ID']);
            }
            $path = ROOTPATH."/writable/folder.json";
            file_put_contents($path,json_encode($file));
            if(@$_GET["format"]=="html"){
                header("Location: /api/google/drive/browse/$id?format=html");
                die();
            }
            return $this->JSONResponse([
                "file" => $file
            ]);
        }
    }

}