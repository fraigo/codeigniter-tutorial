<?php

function send_email($to, $subject, $view, $data=[]){
    $email = \Config\Services::email();
    $config=[];
    $config['protocol'] = 'mail';
    $config['mailPath'] = '/usr/sbin/sendmail';
    $config['mailType']  = 'html';
    $config['charset']  = 'UTF-8';
    $config['wordWrap'] = true;
    $email->initialize($config);
    $email->setFrom('admin@'.$_SERVER['SERVER_NAME'], 'Admin');
    $email->setTo($to);
    $email->setSubject($subject);
    $email->setMessage(view($view,$data));
    $result = $email->send();
    if ($result) {
        return null;
    }
    return $email->printDebugger([]);
}