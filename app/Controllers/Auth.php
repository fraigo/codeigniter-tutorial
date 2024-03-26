<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\Cookie\Cookie;
use CodeIgniter\I18n\Time;

class Auth extends BaseController
{
    private $logoutRedirect = "/auth/login";
    private $loginRedirect = "/";
    protected $modelName = 'App\Models\Users';

    public function form(){
        return $this->layout('auth/form',['errors'=>$this->errors],'login');
    }

    public function recover(){
        return $this->layout('auth/recovery',['errors'=>$this->errors,'success'=>session()->getFlashData('success')],'login');
    }

    public function profile(){
        $userController = new Users();
        $userController->initController($this->request,$this->response,$this->logger);
        return $userController->profile(user_id());
    }

    public function editProfile(){
        $userController = new Users();
        $userController->initController($this->request,$this->response,$this->logger);
        return $userController->editProfile(user_id());
    }
    
    public function updateProfile(){
        $userController = new Users();
        $userController->initController($this->request,$this->response,$this->logger);
        return $userController->updateProfile(user_id());
    }

    public function deleteProfile($userId=null){
        $userId = $userId ?: user_id();
        $users = new \App\Models\Users();
        $user = $users->find($userId);
        if (!$user){
            return $this->notFound();
        }
        $users->update($userId,[
            'user_type' => 5,
            'address' => '',
            'auth_token' => create_token(),
            'avatar_url' => '',
            'city' => '',
            'password' => md5('deleted'.time()),
            'password_token' => '',
            'password_token_expires' => date("Y-m-d H:i:s"),
            'phone' => '',
            'postal_code' => '',
            'push_token' => '',
        ]);
        $authUsers = new \App\Models\AuthUsers();
        $authUsers->deleteProfile($userId);
        $user = $users->find($userId);
        return $this->JSONresponse($user);
    }
    
    public function reset($token){
        $user = $this->model
            ->where('password_token',$token)
            ->where("password_token_expires>",Time::now()->toDateTimeString())
            ->first();
        return $this->layout('auth/reset',[
            'errors'=>$this->errors,
            'success'=>session()->getFlashData('success'),
            'token'=>$token,
            'user'=>$user
        ],'login');
    }

    public function loginWithToken(){
        $data = $this->getVars();
        if (!is_array($data)){
            if ($this->isJson()){
                return $this->JSONResponse(null,400,["message"=>lang('App.invalid_request')]);
            }
            $data=[];
        }
        $user = $this->model
            ->where("password_token",$data["password_token"])
            ->where("password_token_expires>",Time::now()->toDateTimeString())
            ->first();
        if (!$user){
            $this->errors = ["password"=>lang('App.invalid_token')];
            if ($this->isJson()){
                return $this->JSONResponse(null,400,$this->errors);
            }
            return $this->form();
        }
        $result = do_login($user['id'],true);
        if (!$result){
            $this->errors = ["email"=>lang('App.account_unavailable')];
            if ($this->isJson()){
                return $this->JSONResponse(null,400,$this->errors);
            }
            return $this->form();
        }
        if ($this->isJson()){
            return $this->JSONResponse($result);
        }
        return $this->response->redirect($this->loginRedirect);
    }

    public function login()
    {
        $data = $this->getVars();
        if (!is_array($data)){
            if ($this->isJson()){
                return $this->JSONResponse(null,400,["message"=>lang('App.invalid_request')]);
            }
            $data=[];
        }
        $rules = [
            "email" => "required|valid_email",
            "password" => "required",
        ];
        $validation = \Config\Services::validation();
        $validation->setRules($rules);
        if (!$validation->run($data)){
            $this->errors = $validation->getErrors();
            if ($this->isJson()){
                return $this->JSONResponse(null,401,$this->errors);
            }
            return $this->form();
        }
        $user = $this->model
            ->where("email",$data["email"])
            ->where("password",md5($data["password"]))
            ->first();
        if (!$user){
            $this->errors = ["password"=>lang('App.user_password_incorrect')];
            if ($this->isJson()){
                return $this->JSONResponse(null,400,$this->errors);
            }
            return $this->form();
        }
        $result = do_login($user['id'],true);
        if (!$result){
            $this->errors = ["email"=>lang('App.account_unavailable')];
            if ($this->isJson()){
                return $this->JSONResponse(null,400,$this->errors);
            }
            return $this->form();
        }
        if (@$data["remember"]){
            $this->response->setCookie('remember_email',$data["email"],60*60*24*7);
        } else {
            $this->response->deleteCookie('remember_email');
        }
        if ($this->isJson()){
            return $this->JSONResponse($result);
        }
        return $this->response->redirect($this->loginRedirect);
    }

    public function logout(){
        $session = session();
        $session->set('auth', null);
        $session->set('admin', null);
        $session->destroy();
        if ($this->isJson()){
            return $this->JSONResponse([]);
        }
        return $this->response->redirect($this->logoutRedirect);
    }

    function doRegister(){
        $data = $this->getVars();
        $rules = [
            "name" => "required|max_length[64]",
            "email" => "required|valid_email",
            "password" => "required|max_length[32]|password_strength",
            "repeat_password" => [
                "rules" => 'matches[password]',
                "label" => "Repeat Password"
            ],
        ];
        $validation = \Config\Services::validation();
        $validation->setRules($rules);
        if (!$validation->run($data)){
            $this->errors = $validation->getErrors();
            if ($this->isJson()){
                return $this->JSONResponse(null,400,$this->errors);
            }
            return $this->recover();
        }
        $user = $this->model
            ->where("email",$data["email"])
            ->first();
        if ($user) {
            return $this->JSONResponse(null,400,[
                "email" => lang('App.email_already_used')
            ]);
        }
        $token = create_token();
        $result = $this->model->insert([
            "name" => $data["name"],
            "email" => $data["email"],
            "user_type" => getenv('REGISTER_USER_TYPE')?:1,
            "password" =>  $data["password"],
            "repeat_password" =>  $data["repeat_password"],
            "password_token" => $token,
            "password_token_expires" => Time::parse("+24 hours")->toDateTimeString()
        ]);
        $errors = $this->model->errors();
        if ($errors) {
            return $this->JSONResponse(null,400,$errors);
        }
        $user = $this->model
            ->where("email",$data["email"])
            ->first();
        helper('email');
        $baseUrl = (@$data["baseurl"]?:base_url("/auth/verify"));
        $errors = send_email($user['email'], lang('App.verify_email'), "email/verify",[
            "name" => $user["name"],
            "url" => "$baseUrl/$token"
        ]);
        if ($errors){
            $error = explode("<br>",$errors)[0];
            $this->errors = [
                "message" => getenv('email.debug') ? $errors : lang('App.send_error') . ": $error"
            ];
            if ($this->isJson()){
                return $this->JSONResponse(null,400,$this->errors);
            }
            return $this->form();
        }
        $message = lang('App.you_will_receive_an_email_to_verify_your_account');
        if ($this->isJson()){
            return $this->JSONResponse(["message"=>$message]);
        }
        session()->setFlashData("success",$message);
        return redirect()->back();
    }

    function sendVerify(){
        return $this->doRecover("email/verify","/auth/verify","App.verify_email","+24 hours");
    }

    function doRecover($view="email/recovery",$path="/auth/reset",$subject='App.password_recovery_request',$expires="+6 hours"){
        $data = $this->getVars();
        $rules = [
            "email" => "required|valid_email",
        ];
        $validation = \Config\Services::validation();
        $validation->setRules($rules);
        if (!$validation->run($data)){
            $this->errors = $validation->getErrors();
            if ($this->isJson()){
                return $this->JSONResponse(null,400,$this->errors);
            }
            return $this->recover();
        }
        $user = $this->model
            ->where("email",$data["email"])
            ->first();
        $authUsers = new \App\Models\AuthUsers();
        if ($user && !$authUsers->isActive($user['id'])){
            $user = null;
        }
        $token = null;
        if ($user){
            $token = create_token();
            $result = $this->model->update($user['id'],[
                "password_token" => $token,
                "password_token_expires" => Time::parse($expires)->toDateTimeString()
            ]);
            if (!$result){
                $this->errors = [
                    "email" => "Cannot update user"
                ];
                if ($this->isJson()){
                    return $this->JSONResponse(null,400,$this->errors);
                }
                return $this->recover();
            }
            helper('email');
            $baseUrl = (@$data["baseurl"]?:base_url($path));
            $errors = send_email($user['email'], lang($subject), $view,[
                "name" => $user["name"],
                "url" => "$baseUrl/$token"
            ]);
            if ($errors){
                $error = explode("<br>",$errors)[0];
                $this->errors = [
                    "message" => getenv('email.debug') ? $errors : lang('App.send_error') . ": $error"
                ];
                if ($this->isJson()){
                    return $this->JSONResponse(null,400,$this->errors);
                }
                return $this->recover();
            }

        }
        $message = lang('App.recovery_sent');
        if ($this->isJson()){
            return $this->JSONResponse(["message"=>$message]);
        }
        session()->setFlashData("success",$message);
        return redirect()->back();
    }

    function doReset($token){
        $data = $this->getVars();
        $user = $this->model
            ->where("password_token",$token)
            ->where("password_token_expires>",Time::now()->toDateTimeString())
            ->first();
        if (!$user){
            if ($this->isJson()){
                return $this->JSONResponse(null,400,[
                    "token" => lang('App.token_is_invalid_or_has_expired') 
                ]);
            }
            return $this->reset($token);
        }
        $rules = [
            "new_password" => $this->model->getValidationRules()['password'],
            "repeat_password" => [
                "rules" => 'matches[new_password]',
                "label" => 'Repeat Password',
            ]
        ];
        $validation = \Config\Services::validation();
        $validation->setRules($rules);
        if (!$validation->run($data)){
            $this->errors = $validation->getErrors();
            if ($this->isJson()){
                return $this->JSONResponse(null,400,$this->errors);
            }
            return $this->reset($token);
        }
        $data = [
            "password_token" => "",
            "password_token_expires" => "",
            "password" => $data["new_password"]
        ];
        $result = $this->model->update($user['id'],$data);
        if (!$result){
            $this->errors = ["user"=>lang('App.cannot_update_information')];
            if ($this->isJson()){
                return $this->JSONResponse(null,400,$this->errors);
            }
            return $this->reset($token);
        }
        if ($this->isJson()){
            return $this->JSONResponse(["message"=>lang('App.your_account_password_has_been_reset')]);
        }
        session()->setFlashData("success",lang('App.your_account_password_has_been_reset'));
        return redirect()->back();
    }

    function doVerify($token){
        $user = $this->model
            ->where("password_token",$token)
            ->where("password_token_expires>",Time::now()->toDateTimeString())
            ->first();
        if (!$user){
            if ($this->isJson()){
                return $this->JSONResponse(null,400,[
                    "token" => lang('App.token_is_invalid_or_has_expired') 
                ]);
            }
            return $this->reset($token);
        }
        $data = [
            "password_token" => "",
            "password_token_expires" => "",
        ];
        $result = $this->model->update($user['id'],$data);
        if (!$result){
            $this->errors = ["message"=>lang('App.cannot_update_information')];
            if ($this->isJson()){
                return $this->JSONResponse(null,400,$this->errors);
            }
            return $this->reset($token);
        }
        if ($this->isJson()){
            return $this->JSONResponse([
                "message" => lang('App.your_email_has_been_verified'),
                "email" => $user['email']?:"",
            ]);
        }
        session()->setFlashData("success",lang('App.your_email_has_been_verified'));
        return redirect()->back();
    }

    public function pushNotificationsToken(){
        $token = $this->getVars('token');
        $platform = $this->getVars('platform') ?: '';
        $env = $this->getVars('env') ?: '';
        if ($env!=''){
            $token = strtoupper($env).$token;
        }
        if ($platform!=''){
            $token = strtoupper($platform).$token;
        }
        $result = [
            "token" => $token,
            "user_id" => user_id(),
            "new" => false,
        ];
        $users = new \App\Models\Users();
        $user = $users->find(user_id());
        if ($user){
            if ($user['push_token']!=$token){
                $result['new'] = true;
                $query = $users->where('push_token',$token);
                $query->set('push_token','');
                $query->update();
                $result["update"] = $users->update(user_id(),['push_token'=>$token]);
                $events = new \App\Models\Events();
                $events->addEvent("Push Notifications",$result['token'],user_id());    
            }
        }
        return $this->JSONResponse($result);
    }
}
