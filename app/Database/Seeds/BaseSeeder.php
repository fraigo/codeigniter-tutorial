<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BaseSeeder extends Seeder
{
    public function importCSV($table,$file,$truncate=true,$replace=false){
        if ($truncate) {
            echo "Cleanup $table\n";
            $this->db->table($table)->truncate(); 
        }
        $data = @file_get_contents($file);
        if (!$data){
            return;
        }
        echo "Importing $table\n";
        $lines = explode("\n",$data);
        $header = array_shift($lines);
        $fields = explode("\t",trim($header));
        foreach($lines as $lineNum=>$line){
            if (trim($line)=="") continue;
            $values = explode("\t",trim($line));
            // echo implode(",",$values)."\n";
            $row = [];
            foreach($values as $idx=>$val){
                $row[$fields[$idx]]=$val;
            }
            $row["created_at"] = date("Y-m-d H:i:s");
            $row["updated_at"] = date("Y-m-d H:i:s");
            if ($replace){
                $result = @$this->db->table($table)->replace($row);
            } else {
                $result = @$this->db->table($table)->insert($row);
            }
            if (!$result){
                echo "$table ".($lineNum+2).": ".$line."\n";
            }
        }
    }
}