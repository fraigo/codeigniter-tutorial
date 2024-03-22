<?php

namespace App\Controllers;
use CodeIgniter\I18n\Time;

class AppleSignin extends BaseController
{
    var $client = null;
    protected $helpers = ['html','array','auth','module','apple'];
    
    public function token($app=false,$appRedirect=null){
        $redirect = @$_GET['redirect'] ?: "auth/apple";
        if ($app) $redirect = "https://{$_SERVER['HTTP_HOST']}".explode('?',$_SERVER['REQUEST_URI'])[0];
        $code = @$_POST['code'];
        if ($code){
            try{
                $result = apple_signin($redirect, $code);
                $BASEURL = getenv('APP_URL')."/";
                if ($appRedirect){
                    $BASEURL = explode('#',$appRedirect)[0];
                }
                if ($result && @$result['token_data']){
                    $email = @$result['token_data'][1]["email"];
                    $users = new \App\Models\Users();
                    $user = $users->where('email',$email)->first();
                    if ($user){
                        $login = do_login($user['id'],true);
                        if (!$login){
                            return $this->JSONResponse(null,400,["email"=>lang('App.account_unavailable')]);
                        }
                        if ($app){
                            $tokenid = substr($result['access_token'],0,64);
                            $users->update($user['id'],[
                                'password_token' => $tokenid,
                                'password_token_expires' => Time::parse('+1 hours')->toDateTimeString()
                            ]);
                            return $this->layout('auth/apple',['url'=>$BASEURL,'token'=>$tokenid],'login');
                        }
                        return $this->JSONResponse([
                            'user' => $login,
                            'userinfo' => $userinfo
                        ]);
                    } else {
                        if ($email){
                            return $this->layout('auth/notfound',['url'=>$BASEURL,'email'=>$email],'login');
                        } else {
                            return $this->layout('auth/error',[
                                'title'=>'Email unavailable',
                                'message'=>'The email is not shared with out application.<br>We cannot process your login information.',
                                'url'=>$BASEURL],'login');
                        }
                    }
                    return $this->JSONResponse($result);
                } else {
                    return $this->JSONResponse([
                        'redirect' => $redirect,
                        'code'=> $code
                    ],400);
                }
            } catch(\Exception $e) {
                return $this->JSONResponse([
                    'redirect' => $redirect,
                    'code'=> $code,
                    'post'=> $_POST,
                ],400,[
                    'email'=>$e->getMessage(),
                    'line'=>$e->getLine(),
                ]);
            }
            
        }
        return $this->JSONResponse(null,200,['email'=>'Invalid Authentication Token']);
    }

}