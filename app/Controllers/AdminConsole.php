<?php

namespace App\Controllers;

class AdminConsole extends BaseController
{
    private function isAuthenticated(){
        if (!isset($_SERVER['PHP_AUTH_USER'])) return false;
        $user = $_SERVER['PHP_AUTH_USER'];
        $password = $_SERVER['PHP_AUTH_PW'];
        try {
            $db = db_connect();
            $query = $db->query("SELECT count(1) as cnt from users",[]);
            $check = $query->getResultArray();
            $rows = @$check[0]['cnt'];
            if (!$rows) return @$_GET["failsafe"] ? $user=="admin@example.com" && $password=="Admin.123" : false;
            $query = $db->query("SELECT name from users WHERE email=? and password=? and user_type=4",[$user,md5($password)]);
            $result = $query->getResultArray();
            return $result ? count($result) : false;
        } catch (\Throwable $th) {
            return @$_GET["failsafe"] ? $user=="admin@example.com" && $password=="Admin.123" : false;
        }
        
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
        $extra = @$_GET["failsafe"] ? "&failsafe=1" : "";
        $date = date("Y-m-d");
        echo "<h2>Admin Console</h2>";
        echo anchor("/_admin/migrate?$extra","Run Migration",["target"=>"output"]);
        echo anchor("/_admin/rollback?$extra","Rollback Migration",["target"=>"output"]);
        echo anchor("/_admin/refresh?$extra","Refresh Database",["target"=>"output"]);
        echo anchor("/_admin/logs/$date","Current Logs",["target"=>"output"]);
        echo anchor("/_admin/emaillogs/$date","Email Logs",["target"=>"output"]);
        echo anchor("/import","Import",["target"=>"_blank"]);
        $seeds = glob(APPPATH.'/Database/Seeds/*.php');
        echo "<div style='height:100px;overflow-y:auto; border:1px solid #eee;margin-bottom:16px'>";
        foreach ($seeds as $seed){
            $seedName = str_replace(".php","",basename($seed));
            echo anchor("/_admin/appdata?name=$seedName$extra","Seed $seedName",["target"=>"output"]);
        }
        echo "</div>";
        echo '<iframe style="width:100%; height:400px" name="output" ></iframe>';
    }

    public function auth(){
        $this->doAuth();
        $this->response->redirect("./console");
    }

    public function logs($date){
        $logfile = realpath(ROOTPATH."writable/logs/log-$date.log");
        if (file_exists($logfile)){
            $content = file_get_contents($logfile);
            $matches = [];
            preg_match_all('/[A-Z]+ - [0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9] [0-9][0-9]:[0-9][0-9]:[0-9][0-9] --> /',$content,$matches);
            $headers = array_unique($matches[0]);
            foreach($headers as $match){
                $sep = str_repeat("=",32);
                $match2 = str_replace(" --> ","\n",$match);
                $content = str_replace($match,"\n$sep\n$match2",$content);
            }
            header("Content-Type: text/plain");
            echo $content;
            die();
        } else {
            http_response_code(404);
        }
    }

    function table($table=null,$fields=""){
        if (!$table) return ;
        $db = db_connect();
        $query = $db->query("SELECT * from $table",[]);
        $rows = $query->getResultArray();
        if (!@$rows[0]) return;
        $header = $rows[0];
        if (@$_GET["noid"]){
            unset($header["id"]);    
        }
        unset($header["updated_at"]);
        unset($header["created_at"]);
        $headers = array_keys($header);
        if ($fields) {
            $headers = explode(" ",$fields);
        }
        $sep = "\t";
        header("Content-Type: text/plain");
        echo implode($sep,$headers)."\n";
        foreach($rows as $row){
            $values = [];
            foreach($headers as $fld){
                $value = @$row[$fld];
                $value = str_replace("\r","","$value");
                $value = str_replace("\n","",$value);
                if (strpos($value,"\"")!==false){
                    //$value = '"' . str_replace('"','""',$value) . '"';
                }
                $values[] = $value;
            }
            echo implode($sep,$values)."\n";
        }
        die();
    }

    public function emailLogs($date="*"){
        $emailLogger = new \App\Libraries\EmailLogger();
        $path = $emailLogger->logPath();
        $files = glob("$path/email-$date.log");
        arsort($files);
        echo '<head><base target="_blank"></head>';
        echo "<style>
            body{
                font-family: Arial, Helvetica, sans-serif;
            }
            .email-container{
                border: 1px solid #e0e0e0;
                padding:12px;
                display: flex;
                flex-wrap: wrap;
                margin-bottom: 8px;
            }
            .email-info{
                margin-right: 16px;
            }
            .email-body{
                flex: 1;
                min-width: 600px;
            }
        </style>";
        foreach ($files as $file){
            $lines = explode("\n",file_get_contents($file));
            arsort($lines);
            foreach($lines as $line){
                @list($date,$time,$content) = explode(" ",$line,3);
                if ($content=="") continue;
                echo '<div class="email-container">';
                $contents = json_decode($content,true);
                echo '<div class="email-info">';
                echo "<b>Date</b><br>";
                echo "$date $time<br>";
                $body = $contents["body"];
                $headers = urldecode(http_build_query($contents["headers"], "", "\n"));
                $debug = @$contents["debug"];
                unset($contents["body"]);
                foreach($contents as $key=>$value){
                    if (!is_array($value)){
                        echo "<b>$key</b><br>";
                        echo "$value<br>";
                    }
                }
                echo "</div>";
                echo '<div class="email-body">';
                    echo $body;
                    echo '<!-- DEBUG ';
                    echo is_array($debug) ? implode('<br>',$debug) : $debug;
                    echo '-->';
                    echo '<!-- HEADERS '."\n";
                    echo $headers."\n";
                    echo '-->'."\n";
                echo '</div></table>';
                echo '</div></table>';
                echo '</div>';
                echo "</div>";
            }
        }
    }

    public function command($cmd=null){
        $commands = [
            "rollback" => ["php spark migrate:rollback"],
            "refresh" => ["php spark migrate:refresh", "php spark db:seed AppData"],
            "migrate" => ["php spark migrate"],
        ];
        $name = @$_GET["name"];
        if ($name){
            $commands["appdata"] = ["php spark db:seed {$name}"];
        }
        $commandItems = @$commands[$cmd];
        if (!$commandItems){
            return $this->notFound();
        }
        if (!$this->isAuthenticated()){
            return $this->doAuth();
        }
        header("Content-Type: text/plain");
        chdir(ROOTPATH);
        foreach($commandItems as $command){
            echo "==============================\n";
            echo "$command\n";
            echo "==============================\n";
            $result = `$command`;
            echo $result;        
        }
        die();
    }
}