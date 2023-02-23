<?php

namespace App\Controllers;

class AdminConsole extends BaseController
{
    private function isAuthenticated(){
        if (!isset($_SERVER['PHP_AUTH_USER'])) return false;
        $user = $_SERVER['PHP_AUTH_USER'];
        $password = $_SERVER['PHP_AUTH_PW'];
        $db = db_connect();
        $query = $db->query("SELECT name from users WHERE email=? and password=?",[$user,md5($password)]);
        $result = $query->getResultArray();
        return $result ? count($result) : false;
    }

    private function doAuth($force=false){
        if (!@$_SERVER['PHP_AUTH_USER']){
            header('WWW-Authenticate: Basic realm="App"');
            header('HTTP/1.0 401 Unauthorized');
            return $this->JSONResponse(null,401,["success"=>false,"message"=>"Unauthorized"]);
        }    
    }

    public function index(){
        if (!$this->isAuthenticated()){
            return $this->doAuth();
        }
        helper('html');
        echo "<h2>Admin Console</h2>";
        echo anchor("/_admin/migrate","Run Migration")."<br>";
    }

    public function auth(){
        $this->doAuth();
        $this->response->redirect("./console");
    }

    public function command($cmd=null){
        $commands = [
            "migrate" => "php spark migrate"
        ];
        $command = @$commands[$cmd];
        if (!$command){
            return $this->notFound();
        }
        if (!$this->isAuthenticated()){
            return $this->doAuth();
        }
        header("Content-Type: text/plain");
        chdir(ROOTPATH);
        echo "$command\n";
        $result = `$command`;
        echo $result;
        die();
    }
}