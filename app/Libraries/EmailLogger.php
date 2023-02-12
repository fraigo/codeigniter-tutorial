<?php

namespace App\Libraries;

use CodeIgniter\Config\Config;
use CodeIgniter\Email\Email;

class EmailLogger extends Email{

    public function logPath(){
        return WRITEPATH.'logs';
    }

    protected function logEmail($type, $success, $debug = []){
        $recipients = is_array($this->recipients) ? implode(', ', $this->recipients) : $this->recipients;

        $from = $this->cleanEmail($this->headers['Return-Path']);

        $log = [
            "from" => $from,
            "recipients" => $recipients, 
            "subject" => mb_convert_encoding(mb_decode_mimeheader($this->subject),'UTF-8'),
            "status" => $success ? "Success" : "Error",
            "protocol" => $type,
            "body" => $this->body,
            "headers" => $this->headers,
            "debug" => $debug
        ];
        error_log(date("Y-m-d H:i:s").' '.json_encode($log)."\n", 3, $this->logPath().'/email-'.date("Y-m-d").'.log');
        return true;
    }

    protected function spoolEmail()
    {
        $result = parent::spoolEmail();
        $this->logEmail($this->getProtocol(), $result, $this->debugMessage);
        return $result;
    }

}