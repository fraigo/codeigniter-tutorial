<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Auth extends BaseController
{
    private $errors = null;
    private $logoutRedirect = "/";
    private $loginRedirect = "/";

    public function form(){
        return view('auth/form',['errors'=>$this->errors]);
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
        $users = new \App\Models\Users();
        $user = $users
                    ->where("email",$data["email"])
                    ->where("password",md5($data["password"]))
                    ->first();
        if (!$user){
            $this->errors = ["User or password is incorrect"];
            return $this->form();
        }
        $user["login_at"]=gmdate("Y-m-d H:i:s");
        $users->update($user["id"],$user);
        $session = session();
        $session->set('auth', $user);
        return $this->response->redirect($this->loginRedirect);
    }

    public function logout(){
        $session = session();
        $session->set('auth', null);
        return $this->response->redirect($this->logoutRedirect);
    }
}
