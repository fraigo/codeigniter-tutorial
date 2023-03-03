<?php

function send_email($to, $subject, $view, $data=[]){
    $email = \Config\Services::email();
    $config=[];
    $config['protocol'] = getenv('email.protocol')?:'mail';
    $config['mailPath'] = getenv('email.mailPath')?:'/usr/sbin/sendmail';
    $config['mailType'] = getenv('email.mailType')?:'html';
    $config['charset']  = getenv('email.charset')?:'UTF-8';
    $config['wordWrap'] = getenv('email.wordWrap') ? true : false;
    $config['SMTPHost'] = getenv('email.SMTPHost')?:'localhost';
    $config['SMTPUser'] = getenv('email.SMTPUser')?:'project.1';
    $config['SMTPPass'] = getenv('email.SMTPPass')?:'secret.1';
    $config['SMTPPort'] = getenv('email.SMTPPort')?:'1025';
    $config['SMTPCrypto'] = getenv('email.SMTPCrypto')?:'';
    $newLines = [
        '\r\n' => "\r\n",
        '\n' => "\n",
    ];
    $config['newline'] = @$newLines[getenv('email.newline')]?:"\r\n";
    $email->initialize($config);
    $email->setFrom(getenv('email.from')?:'admin@'.$_SERVER['SERVER_NAME'], 'Admin');
    $email->setTo($to);
    $email->setSubject($subject);
    $email->setMessage(view($view,$data));
    $result = $email->send();
    if ($result) {
        return null;
    }
    return $email->printDebugger([]);
}