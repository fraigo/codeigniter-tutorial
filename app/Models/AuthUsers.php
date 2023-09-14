<?php

namespace App\Models;

use CodeIgniter\Model;

class AuthUsers extends Users
{

    static function deleteProfile($userId){
        
    }

    static function isActive($userId){
        $users = new \App\Models\Users();
        $user = $users->find($userId);
        if (!$user || $user["user_type"] == 5){
            return false;
        }
        return true;
    }

}