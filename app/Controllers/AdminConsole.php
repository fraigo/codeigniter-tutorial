<?php

namespace App\Controllers;

class AdminConsole extends BaseController
{
    private function isAuthenticated(){
        helper('auth');
        if (is_admin()) return true;
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

    private function consoleLink($link,$label,$attrs=[]){
        if (!$attrs) $attrs = [];
        $attrs["onclick"] = "return confirm('Continue with '+this.innerText+' ?')";
        return anchor($link,$label,$attrs);
    }

    public function emailtest($email=null){
        helper("email");
        print_r([$email]);
        if (@$_GET['config']){
            print_r(email_config());
        }
        $date = date("Ymd H:i");
        $result = send_email($email, "Email Test $date", "email/test", [], []);
        if ($result){
            print_r($result);
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
        echo $this->consoleLink("/_admin/composer?$extra","Composer Update",["target"=>"output"]);
        echo $this->consoleLink("/_admin/migrate?$extra","Run Migration",["target"=>"output"]);
        echo $this->consoleLink("/_admin/rollback?$extra","Rollback Migration",["target"=>"output"]);
        echo $this->consoleLink("/_admin/refresh?$extra","Refresh Database",["target"=>"output"]);
        echo $this->consoleLink("/_admin/zipuploads?$extra","Backup Images",["target"=>"output"]);
        echo $this->consoleLink("/_admin/unzipuploads?$extra","Restore Images",["target"=>"output"]);
        echo $this->consoleLink("/_admin/download/images.zip?$extra","Download Images",["target"=>"output"]);
        echo $this->consoleLink("/_admin/logs/$date","Current Logs",["target"=>"output"]);
        echo $this->consoleLink("/_admin/logs/$date/api","API Logs",["target"=>"output"]);
        echo $this->consoleLink("/_admin/emaillogs/$date","Email Logs",["target"=>"output"]);
        echo $this->consoleLink("/_admin/patches?$extra","Vendor Patches",["target"=>"output"]);
        echo $this->consoleLink("/import","Import",["target"=>"_blank"]);
        $seeds = glob(APPPATH.'/Database/Seeds/*.php');
        echo "<div style='height:100px;overflow-y:auto; border:1px solid #eee;margin-bottom:16px'>";
        foreach ($seeds as $seed){
            $seedName = str_replace(".php","",basename($seed));
            echo $this->consoleLink("/_admin/appdata?name=$seedName$extra","Seed $seedName",["target"=>"output"]);
        }
        echo "</div>";
        echo "<div><small>".date("Y-m-d H:i:s",filemtime(__FILE__))."<small></div>";
        echo '<iframe style="width:100%; height:400px" name="output" ></iframe>';
    }

    public function auth(){
        $this->doAuth();
        $this->response->redirect("./console");
    }

    public function logs($date,$type="log"){
        $logfile = realpath(ROOTPATH."writable/logs/$type-$date.log");
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
        $cond = "";
        $params = [];
        $sort = "";
        if (@$_GET["min_id"]>0){
            $cond = "WHERE id>=?";
            $params = [$_GET["min_id"]];
        }
        if (@$_GET["sort"]){
            $sort = "ORDER BY {$_GET["sort"]}";
        }
        $query = $db->query("SELECT * from $table $cond $sort",$params);
        $rows = $query->getResultArray();
        if (!@$rows[0]) return;
        $header = $rows[0];
        if (@$_GET["noid"]){
            unset($header["id"]);    
        }
        if (@$_GET["notimestamp"]){
            unset($header["updated_at"]);
            unset($header["created_at"]);
        }
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
                $value = str_replace("0000-00-00 00:00:00","",$value);
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
                display: flex;
                flex-wrap: wrap;
                margin-bottom: 8px;
            }
            .email-info{
                margin-right: 16px;
                padding:12px;
            }
            .email-body{
                flex: 1;
                min-width: 600px;
                margin-left
            }
            table{
                max-width: 98vw;
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
                $body = str_replace("cid:logo.png@",getenv('app.logo').'?',$body);
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

    public function download($id){
        chdir(ROOTPATH);
        $files = [
            "images.zip" => "writable/images.zip",
            "custom.zip" => "writable/custom.zip"
        ];
        $file = @$files[$id];
        if ($file && file_exists($file)){
            header("Content-type: application/octet-stream");
            readfile($file);
        }
        die();
    }

    public function command($cmd=null){
        $composer_cmd = 'composer';
        $commands = [
            "composer" => ["$composer_cmd update --no-progress 2>&1","unzip -o vendor_patches.zip"],
            "rollback" => ["php spark migrate:rollback"],
            "refresh" => ["php spark migrate:refresh", "php spark db:seed AppData"],
            "migrate" => ["php spark migrate"],
            "patches" => ["unzip -o vendor_patches.zip"],
            "zipuploads" => ["rm -f writable/images.zip","zip -o writable/images.zip writable/uploads/images/*.png writable/uploads/images/*.jpg","zip -o writable/documents.zip writable/documents/*.pdf writable/documents/*.jpeg writable/documents/*.jpg"],
            "unzipuploads" => ["unzip -o writable/images.zip","unzip -o writable/documents.zip"],
            "zipcustom" => ["zip -r -o writable/custom.zip public/img/uniform/* public/pdf/*"],
            "unzipcustom" => ["unzip -o writable/custom.zip"],
        ];
        $name = @$_GET["name"];
        if ($name){
            $commands["appdata"] = ["php spark db:seed {$name}"];
        }
        $spark_command = @$_GET["command"];
        if ($composer_cmd){
            $commands["spark"] = ["php spark $spark_command"];
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

    public function schema(){
        header("Content-Type: text/plain");
        $data = [];
        $data["name"] = getenv('app.name')?:'App';
        $metadata = [
            "name" => strtolower($data["name"]),
            "tables" => [],
        ];
        $db = db_connect();
        $tables = $db->listTables();
        //asort($tables);
        $types = [
            "integer" => "int"
        ];
        foreach($tables as $table){
            if ($table=="migrations") continue;
            $tableMetadata = [
                "name" => $table,
                "fields" => [],
                "relationships" => [],
                "indexes" => [],
            ];
            $fields = $db->getFieldData($table);
            $pks = [];
            foreach ($fields as $field) {
                $fieldData = [
                    "name" => $field->name,
                    "type" => strtolower(@$types[strtolower($field->type)]?:$field->type),
                    "size" => $field->max_length,
                ];
                if ($field->max_length){
                    $fieldData["size"] = $field->max_length;
                }
                if ($field->nullable){
                    $fieldData["null"] = true;
                }
                if ($field->primary_key){
                    $fieldData["primary_key"] = true;
                    $fieldData["auto_increment"] = true;
                }
                $tableMetadata["fields"][$field->name] = $fieldData;
                if ($field->primary_key) $pks[] = $field->name;
            }
            //ksort($tableMetadata["fields"]);
            $tableMetadata["fields"] = array_values($tableMetadata["fields"]);
            $metadata["tables"][] = $tableMetadata;
        }
        $data["databases"] = [$metadata];
        
        echo json_encode($data,JSON_PRETTY_PRINT);
        die();
    }

    public function smstest($number=null){
        helper('sms');
        $result = send_sms($number?:getenv('SMS_TEST_PHONE'),"Testing SMS Service");
        return $this->JSONResponse($result);
    }

    public function sqlcommand(){
        $db = db_connect();
        $sql = @$_GET["sql"];
        $type = @$_GET["type"] ?: "json";
        if (strpos(strtolower("$sql"),"select ")===0){
            if ($type=="csv"){
                header("Content-Type: text/plain");
            } else {
                header("Content-Type: text/json");
            }
            try {
                $result = [ 
                    "sql" => $sql,
                    "result" => $db->query($sql,[])->getResultArray() 
                ];
            } catch (\Throwable $e) {
                $result = [
                    "error" => $e->getMessage()
                ];
            }
            if ($type=="csv" && $result['result']){
                $res = $result['result'];
                echo implode("\t", array_keys($res[0]));
                echo "\n";
                foreach($res as $idx=>$row){
                    echo implode("\t", $row);
                    echo "\n";
                }
            } else {
                echo json_encode($result,JSON_PRETTY_PRINT);
            }
        }
        else if($sql){
            header("Content-Type: text/json");
            try {
                $result = [ "result" => $db->simpleQuery($sql) ];
            } catch (\Throwable $e) {
                $result = [
                    "error" => $e->getMessage()
                ];
            }
            echo json_encode($result,JSON_PRETTY_PRINT);
        }
        else {
            return view('default',[
                "content" => "<div class='container' >
                <form method=GET id=sqlform >
                    <div class='form-item'>
                        <textarea placeholder='SQL command' name=sql style='height:200px;font-family:Courier, monospace'></textarea>
                    </div>
                    <div class='form-item'>
                        <input type='button' value='Last Query' onclick=\"this.form.sql.value = localStorage.getItem('last_query')\">
                        <select style='max-width:200px' id=query_history onchange=\"this.form.sql.value = this.value\" >
                            <option value=''>SQL History</option>
                        </select>
                        <select style='max-width:100px' name=type >
                            <option value='json'>JSON</option>
                            <option value='csv'>CSV</option>
                        </select>
                        <div style=\"flex:1\"></div>
                        <input type=submit style='font-weight:bold' value=Submit onclick=\"saveQuery(this.form)\" >
                    </div>
                </form></div>
                <script>
                    function saveQuery(frm){
                        var queries = localStorage.getItem('query_log') ? JSON.parse(localStorage.getItem('query_log')) : []
                        localStorage.setItem('last_query',frm.sql.value)
                        var pos = queries.indexOf(frm.sql.value)
                        if (pos>=0) {
                            queries.splice(pos,1)
                        }
                        queries.push(frm.sql.value)
                        localStorage.setItem('query_log', JSON.stringify(queries)) 
                    }
                    function loadHistory(){
                        var queries = localStorage.getItem('query_log') ? JSON.parse(localStorage.getItem('query_log')) : []
                        var sel = document.querySelector('#query_history');
                        for(var idx in queries){
                            var opt = document.createElement('option');
                            opt.value = queries[idx];
                            opt.text = queries[idx];
                            sel.appendChild(opt);
                        }
                    }
                    loadHistory();
                </script>"
            ]);
        }
        die();
    }

    function uploadImageForm(){
        return view('default',['content'=>view('admin/upload-image.php')]);
    }

    function uploadImage(){
        $error = !@$_FILES['file'] || $_FILES['file']['error'];
        $success = false;
        if (!$error){
            $path = $_POST["path"];
            $filename = @$_POST['name']?:$_FILES['file']['name'];
            $source = $_FILES['file']['tmp_name'];
            $msg = "";
            if (!file_exists($path) && is_dir($path)){
                $msg = "Path is invalid";
            }
            $target = ROOTPATH."/$path/$filename";
            $success = @move_uploaded_file($source,$target);
            if ($success){
                $msg = "File Upladed Successfully";
            } else {
                $msg = "Error uploading file $source to $target";
            }
        } else {
            $msg = "Upload Error";
        }
       
        return view('default',['content'=>view('admin/upload-image.php')."<br><i>$msg</i>"]);
    }

    function editor($filename = null,$extra=null){
        $filename = $filename ?: ".env";
        if ($extra) $filename = "$filename/$extra";
        return view('default',['content'=>view('admin/text-editor.php',['filename'=>$filename])]);
    }

    function pushnotification($token){
        helper('pushnotifications');
        $users = new \App\Models\Users();
        $user = $users->where('push_token',$token)->first();
        $appname = getenv('app.name')?:'App';
        $message = "This is a test notification from $appname";
        if ($user){
            $userId = $user["id"];
            $notification = new \App\Models\UserNotifications();
            $pending = $notification
                ->where([
                    'user_id'=>$userId,
                    'read'=>0
                ])
                ->where("user_notifications.created_at>=",date("Y-m-d",strtotime("-1 month")))
                ->findColumn('id');
            $pendingItems = $pending ? count($pending) : 0;
            $message = "You have $pendingItems unread notifications";
        }
        $extra = ["link"=>"#/profile"];
        $extra['payload'] = json_encode($extra);
        $result = push_notification($token,"Test Notification",$message,1,$extra);
        return $this->JSONResponse($result);
    }
}