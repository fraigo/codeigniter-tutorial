<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Auth extends BaseController
{
    public function login()
    {
        $data = $this->request->getVar();
        $users = new \App\Models\Users();
        $user = $users
                    ->where("email",$data["email"])
                    ->where("password",md5($data["password"]))
                    ->first();
        if (!$user){
            die("User or password is incorrect");
        }
        $user["login_at"]=gmdate("Y-m-d H:i:s");
        $users->update($user["id"],$user);
        $session = session();
        $session->set('auth', $user);
        return $this->response->redirect('/');
    }

    public function logout(){
        $session = session();
        $session->set('auth', null);
        return $this->response->redirect('/');
    }
}
