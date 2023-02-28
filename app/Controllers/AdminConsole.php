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
        if (!@$_SERVER['PHP_AUTH_USER'] || !$this->isAuthenticated()){
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
        echo "<style>
        a{
            display: block;
            padding: 4px;
            text-decoration: none;
        }
        </style>";
        echo "<h2>Admin Console</h2>";
        echo anchor("/_admin/migrate","Run Migration",["target"=>"output"])."<br>";
        echo anchor("/import    ","Import",["target"=>"_blank"])."<br>";
        $seeds = glob(APPPATH.'/Database/Seeds/*.php');
        foreach ($seeds as $seed){
            $seedName = str_replace(".php","",basename($seed));
            echo anchor("/_admin/appdata?name=$seedName","Seed $seedName",["target"=>"output"])."<br>";
        }
        echo '<iframe style="width:100%; height:400px" name="output" ></iframe>';
    }

    public function auth(){
        $this->doAuth();
        $this->response->redirect("./console");
    }

    public function command($cmd=null){
        $commands = [
            "rollback" => "php spark migrate",
            "migrate" => "php spark migrate",
        ];
        $name = @$_GET["name"];
        if ($name){
            $commands["appdata"] = "php spark db:seed {$name}";
        }
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