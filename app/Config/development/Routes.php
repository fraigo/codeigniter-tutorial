<?php

$routes->get('/logs/default', static function () {
    $response = \Config\Services::response();
    $date = date("Y-m-d");
    $response->setContentType("text/plain");
    return file_get_contents(WRITEPATH."/logs/log-$date.log");
});

$routes->get('/logs/email', static function () {
    $emailLogger = new \App\Libraries\EmailLogger();
    $path = $emailLogger->logPath();
    $files = glob("$path/email-*.log");
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
    
});
?>
