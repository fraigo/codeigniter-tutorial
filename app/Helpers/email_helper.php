<?php

$EMAIL_ATTACHMENTS = [];

function send_email($to, $subject, $view, $data=[],$attachments=[]){
    global $EMAIL_ATTACHMENTS;
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
    $email->setFrom(getenv('email.from')?:'admin@'.$_SERVER['SERVER_NAME'], getenv('email.fromName')?:getenv('app.name'));
    $email->setTo($to);
    $email->setSubject($subject);
    $htmlContent = view($view,$data);
    foreach($EMAIL_ATTACHMENTS as $item){
        $email->attach($item["file"],@$item["disposition"]?:'',@$item["name"],@$item["mime"]?:'');
        $cid = $email->setAttachmentCID($item["file"]);
        $htmlContent = str_replace($item['url'],"cid:$cid",$htmlContent);
    }
    foreach($attachments as $item){
        $email->attach($item["file"],@$item["disposition"]?:'',@$item["name"],@$item["mime"]?:'');
    }
    $email->setMessage($htmlContent);
    $result = $email->send();
    if ($result) {
        return null;
    }
    return $email->printDebugger([]);
}

function imageAttachment($publicPath,$mime=null){
    global $EMAIL_ATTACHMENTS;
    if (!$publicPath) return null;
    $name=basename($publicPath);
    $parts = explode(".",$name);
    $ext = strtolower(array_pop($parts));
    $result = [
        "url" => base_url().$publicPath,
    ];
    if (!$mime) $mime = "image/$ext";
    $fullPath = realpath(ROOTPATH.'/public'.$publicPath);
    if ($fullPath && file_exists($fullPath)){
        $result["file"] = $fullPath;
        $result["name"] = $name;  
    }
    $EMAIL_ATTACHMENTS[$fullPath] = $result;
    return $result;
}