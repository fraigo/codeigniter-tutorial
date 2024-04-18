<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$EMAIL_ATTACHMENTS = [];

function clear_attachments(){
    global $EMAIL_ATTACHMENTS;
    if ($EMAIL_ATTACHMENTS)
    foreach($EMAIL_ATTACHMENTS as $idx=>$item){
        unset($EMAIL_ATTACHMENTS[$idx]);
    }
}

function email_config(){
    $config=[];
    $config['protocol'] = getenv('email.protocol')?:'mail';
    $config['mailPath'] = getenv('email.mailPath')?:'/usr/sbin/sendmail';
    $config['mailType'] = getenv('email.mailType')?:'html';
    $config['charset']  = getenv('email.charset')?:'UTF-8';
    $config['wordWrap'] = getenv('email.wordWrap') == 1 ? true : false;
    $config['SMTPHost'] = getenv('email.SMTPHost')?:'localhost';
    $config['SMTPUser'] = getenv('email.SMTPUser')?:'project.1';
    $config['SMTPPass'] = getenv('email.SMTPPass')?:'secret.1';
    $config['SMTPPort'] = getenv('email.SMTPPort')?:'1025';
    $config['SMTPCrypto'] = getenv('email.SMTPCrypto')?:'';
    return $config;
}

function send_email($to, $subject, $view, $data=[],$attachments=[], $return=false){
    global $EMAIL_ATTACHMENTS;

    $config = email_config();
    $subject = (getenv("TEST_SUBJECT") ?: "").$subject.(getenv("TEST_EMAIL") ? " ($to)" : "");
    $to = getenv("TEST_EMAIL") ?: $to;

    $htmlMessage = "".view($view,$data);
    $htmlContent = $htmlMessage;
    $textMessage = preg_replace( "/\n\s+/", "\n", rtrim(html_entity_decode(strip_tags($htmlMessage))));


    try{
        $mail = new PHPMailer();
        //use PHPMailer\PHPMailer\SMTP;
        //use PHPMailer\PHPMailer\Exception;
        if ($return){
            ob_start();
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        }
        $mail->isSMTP();                                            //Send using SMTP
        $mail->CharSet    = $config['charset'];
        $mail->Host       = $config['SMTPHost'];                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = $config['SMTPUser'];                     //SMTP username
        $mail->Password   = $config['SMTPPass'];
        if ($config['SMTPCrypto']){
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        }
        $mail->Port       = $config['SMTPPort']*1;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
    
        //Recipients
        $name = getenv('email.fromName')?:getenv('app.name');
        $mail->setFrom(getenv('email.from'), $name);
        $mail->addAddress($to);     //Add a recipient
        if (getenv('email.replyto')){
            $mail->addReplyTo(getenv('email.replyto'));
        }

        if (@$EMAIL_ATTACHMENTS)
        foreach($EMAIL_ATTACHMENTS as $item){
            if (!@$item['file']) continue;
            $name = basename($item['file']);
            $cid = str_replace(".","_",$name);
            $mail->AddEmbeddedImage($item["file"], $cid, $name);
            $htmlContent = str_replace($item['url'],"cid:$cid",$htmlContent);
        }    
            
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $htmlContent;
        $mail->AltBody = $textMessage;

        $mail->send();

        if ($return){
            $result = ob_get_clean();
            return explode("\n",$result);
        }
        return null;
    } catch (Exception $e) {
        return [
            "errors" => $mail->ErrorInfo
        ];
    }


}

function send_emai2l($to, $subject, $view, $data=[],$attachments=[], $return=false){
    global $EMAIL_ATTACHMENTS;

    $subject = (getenv("TEST_SUBJECT") ?: "").$subject.(getenv("TEST_EMAIL") ? " ($to)" : "");
    $to = getenv("TEST_EMAIL") ?: $to;
    
    $config = email_config();
    $email = \Config\Services::email();
    
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
    $htmlMessage = "".view($view,$data);
    $htmlContent = $htmlMessage;
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
    $textMessage = preg_replace( "/\n\s+/", "\n", rtrim(html_entity_decode(strip_tags($htmlMessage))));
    $email->setAltMessage(preg_replace( "/\n\s+/", "\n", $textMessage));
    $result = $email->send();
    if ($result) {
        $email->clear(true);
        return $return ? $email : null;
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
    $button = str_replace("{label}",@$label?:lang('App.view_details'),$button);
    return $button;
}