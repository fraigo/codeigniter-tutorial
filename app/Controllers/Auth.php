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

    public function login()
    {
        $data = json_decode(json_encode($this->request->getVar()),true);
        if (!is_array($data)){
            if ($this->isJson()){
                return $this->JSONResponse(null,400,["message"=>"Invalid Request"]);
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
            $this->errors = ["password"=>"User or password is incorrect"];
            if ($this->isJson()){
                return $this->JSONResponse(null,400,$this->errors);
            }
            return $this->form();
        }
        $result = do_login($user['id']);
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

    function doRecover(){
        $data = $this->request->getVar();
        $rules = [
            "email" => "required|valid_email",
        ];
        $validation = \Config\Services::validation();
        $validation->setRules($rules);
        if (!$validation->run($data)){
            $this->errors = $validation->getErrors();
            return $this->recover();
        }
        $user = $this->model
            ->where("email",$data["email"])
            ->first();
        $token = null;
        if ($user){
            $token = create_token();
            $result = $this->model->update($user['id'],[
                "password_token" => $token,
                "password_token_expires" => Time::parse("+6 hours")->toDateTimeString()
            ]);
            if (!$result){
                $this->errors = [
                    "email" => "Cannot update user"
                ];
                return $this->recover();
            }
            helper('email');
            $errors = send_email($data["email"], "Password Recovery request", "email/recovery",[
                "name" => $user["name"],
                "url" => base_url()."/auth/reset/$token"
            ]);
            if ($errors){
                $error = explode("<br>",$errors)[0];
                $this->errors = [
                    "message" => "Send Error: $error"
                ];
                return $this->recover();
            }

        }
        session()->setFlashData("success","If your email is registered, you will receive an email with account recovery instructions.");
        return redirect()->back();
    }

    function doReset($token){
        $data = $this->request->getVar();
        $user = $this->model
            ->where("password_token",$token)
            ->where("password_token_expires>",Time::now()->toDateTimeString())
            ->first();
        if (!$user){
            return $this->reset($token);
        }
        $rules = [
            "new_password" => $this->model->getValidationRules()['password'],
            "repeat_password" => [
                "rules" => 'matches[new_password]',
                "label" => "Repeat password"
            ]
        ];
        $validation = \Config\Services::validation();
        $validation->setRules($rules);
        if (!$validation->run($data)){
            $this->errors = $validation->getErrors();
            return $this->reset($token);
        }
        $data = [
            "password_token" => "",
            "password_token_expires" => "",
            "password" => $data["new_password"]
        ];
        $result = $this->model->update($user['id'],$data);
        if (!$result){
            $this->errors = ["user"=>"Cannot update user with password"];
            return $this->reset($token);
        }
        session()->setFlashData("success","Your account password was reset.");
        return redirect()->back();
    }
}
