<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BaseSeeder extends Seeder
{
    public function importCSV($table,$file,$truncate=true,$replace=false,$nullableFields=[]){
        if ($truncate) {
            echo "Cleanup $table\n";
            $this->db->table($table)->truncate(); 
        }
        $data = @file_get_contents($file);
        if (!$data){
            return;
        }
        echo "Importing $table\n";
        $start = microtime(true);
        $lines = explode("\n",$data);
        $header = array_shift($lines);
        $fields = explode("\t",trim($header));
        $total = 0;
        $errors = 0;
        $start = microtime(true);
        $timer = microtime(true);
        $count = count($lines) - 1;
        foreach($lines as $lineNum=>$line){
            if (trim($line)=="") continue;
            $values = explode("\t",trim($line));
            // echo implode(",",$values)."\n";
            $row = [];
            foreach($values as $idx=>$val){
                $row[$fields[$idx]]=$val;
            }
            foreach($nullableFields as $fld=>$nullable){
                if (@$row[$fld]===""){
                    $row[$fld]=null;
                }
                if (@$row[$fld]===null && $nullable){
                    unset($row[$fld]);
                }
            }
            if (!@$row["created_at"])
            $row["created_at"] = date("Y-m-d H:i:s");
            if (!@$row["updated_at"])
            $row["updated_at"] = date("Y-m-d H:i:s");
            if ($replace){
                $result = @$this->db->table($table)->replace($row);
            } else {
                $result = @$this->db->table($table)->insert($row);
            }
            $total++;
            $newtime = microtime(true);
            if ($newtime-$timer>10){
                $diff = round($newtime - $start);
                $prec = round($total * 100 / $count);
                echo "Imported $prec% $total/$count lines ($diff sec) ...\n";
                $timer = $newtime;
            }
            if (!$result){
                $errors++;
                echo "$table ".($lineNum+2).": ".$line."\n";
            }
        }
        $duration = round((microtime(true) - $start)*100)/100;
        echo "Duration: $duration seg. $total lines ($errors errors)\n";
    }
}