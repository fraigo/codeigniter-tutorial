<?php

$EMAIL_ATTACHMENTS = [];

function clear_attachments(){
    global $EMAIL_ATTACHMENTS;
    if ($EMAIL_ATTACHMENTS)
    foreach($EMAIL_ATTACHMENTS as $idx=>$item){
        unset($EMAIL_ATTACHMENTS[$idx]);
    }
}

function send_email($to, $subject, $view, $data=[],$attachments=[]){
    global $EMAIL_ATTACHMENTS;

    $subject = (getenv("TEST_SUBJECT") ?: "").$subject.(getenv("TEST_EMAIL") ? " ($to)" : "");
    $to = getenv("TEST_EMAIL") ?: $to;
    

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
    if (getenv('email.replyto'))
    $email->setReplyTo(getenv('email.replyto'));
    $email->setSubject($subject);
    $htmlContent = str_replace("\n"," ",view($view,$data));
    if (@$EMAIL_ATTACHMENTS)
    foreach($EMAIL_ATTACHMENTS as $item){
	if (!@$item['file']) continue;
        $email->attach($item["file"],@$item["disposition"]?:'',@$item["name"],@$item["mime"]?:'');
        $cid = $email->setAttachmentCID($item["file"]);
        $htmlContent = str_replace($item['url'],"cid:$cid",$htmlContent);
    }
    clear_attachments();
    foreach($attachments as $item){
        $email->attach($item["file"],@$item["disposition"]?:'',@$item["name"],@$item["mime"]?:'');
    }
    $email->setMessage($htmlContent);
    $result = $email->send();
    if ($result) {
        $email->clear(true);
        return null;
    }
    $debug = $email->printDebugger([]);
    $email->clear(true);
    return $debug;
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

function email_button($link,$label=null,$bgcolor="#d28e19",$color="#f0f0f0",$template = "[url]{label}[/url]"){
    if (!$link) return '';
    $button = str_replace('[url]','<a href="{url}" style="text-decoration:none !important;"><div style="padding:8px 20px;background-color:'.$bgcolor.';color:'.$color.';text-decoration:none;border-radius:5px 5px;display:inline-block"><span style="color:#fff">',$template);
    $button = str_replace('[/url]','</span></div></a>',$button);
    $button = str_replace("{url}",@$link?:'',$button);
    $button = str_replace("{label}",@$label?:'View Details',$button);
    return $button;
}