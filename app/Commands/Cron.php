<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class Cron extends BaseCommand
{
    protected $group       = 'Cron';
    protected $name        = 'cron';
    protected $description = 'Run automated tasks daily.';

    private $db;

    public function run($params)
    {
        $this->db = db_connect();
        header("Content-type: text/plain");
        if ($params[0]=="daily"){
            $this->daily(@$params[1]);
        }
        if ($params[0]=="hourly"){
            $this->hourly(@$params[1]);
        }
    }

    public function daily($param1=null){
        $this->zipLogs($param1);
        $this->dailyCleanup($param1);
    }

    public function hourly($param1=null){
        $this->hourlyCleanup($param1);
    }

    private function zipLogs($param1){
        chdir(ROOTPATH."/writable");
        $logs = glob("logs/*.log");
        $logDate = date("Ym",strtotime("-3 month"));
        $maxDate = date("Y-m-",strtotime("-2 month"))."01";
        $logFile = "logs_$logDate.zip";
        echo "zipLogs\n";
        echo "$logFile\n";
        $added = 0;
        foreach($logs as $file){
            if (filemtime($file)<strtotime("$maxDate")){
                $cmd = "zip -m $logFile $file";
                $result = `$cmd`;
                $added++;
                echo "$result\n";
            }
        }
        if ($added==0) echo "No logs\n";

    }

    private function dailyCleanup($param1){
        echo "dailyCleanup\n";

    }

    private function hourlyCleanup($param1){
        echo "hourlyCleanup\n";
        
    }

}
