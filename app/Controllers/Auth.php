<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Auth extends BaseController
{
    private $logoutRedirect = "/auth/login";
    private $loginRedirect = "/";
    protected $modelName = 'App\Models\Users';

    public function form(){
        return $this->layout('auth/form',['errors'=>$this->errors],'login');
    }

    public function login()
    {
        $data = $this->request->getVar();
        $rules = [
            "email" => "required|valid_email",
            "password" => "required",
        ];
        $validation = \Config\Services::validation();
        $validation->setRules($rules);
        if (!$validation->run($data)){
            $this->errors = $validation->getErrors();
            return $this->form();
        }
        $user = $this->model
                    ->where("email",$data["email"])
                    ->where("password",md5($data["password"]))
                    ->first();
        if (!$user){
            $this->errors = ["password"=>"User or password is incorrect"];
            return $this->form();
        }
        $userTypes = new \App\Models\UserTypes();
        $userType = $userTypes->find($user["user_type"]);
        $perm = new \App\Models\Permissions();
        $permissions = $perm->select(['module','access'])
            ->where("user_type_id",$user["user_type"])
            ->findAll();
        $this->model->update($user["id"],[
            "login_at" => gmdate("Y-m-d H:i:s")
        ]);
        $session = session();
        $session->set('auth', $user);
        $session->set('admin', $userType["access"]==4);
        $session->set('profile', $userType);
        $session->set('permissions', array_column($permissions,"access","module"));
        return $this->response->redirect($this->loginRedirect);
    }

    public function logout(){
        $session = session();
        $session->set('auth', null);
        $session->set('admin', null);
        return $this->response->redirect($this->logoutRedirect);
    }
}
