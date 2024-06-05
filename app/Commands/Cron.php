<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class Cron extends BaseCommand
{
    protected $group       = 'Cron';
    protected $name        = 'cron';
    protected $description = 'Run automated tasks daily.';

    public function run($params)
    {
        if ($params[0]=="daily"){
            $this->daily(@$params[1]);
        }
    }

    public function daily($param1=null){
        
    }

}
