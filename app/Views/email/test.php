<?php
$url = getenv('APP_URL') . '/test/email?code=1234567890-1234567890';
$name = 'Test User';
?><!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Email Test</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>
<body>
<p style="margin:16px 0">
 <img src="<?php echo imageAttachment(getenv('app.logo'))['url']?>" 
alt="Logo" height="92" />
</p>
<p style="margin:16px 0">
 <?=lang('App.hello',[$name])?>,<br>
</p>
<p style="margin:16px 0"
><?=lang('App.verify_email_message',[getenv('app.name')])?><br>
</p>
<p style="margin:16px 0">
<a href="<?php echo $url ?>"
><?php echo $url ?>
</a>
</p>
<p style="margin:16px 0">
<b><?php echo @$sender?:lang('App.email_sender',[getenv('app.name')]) ?></b>
</p>
<p style="margin:16px 0">
<?=@$footer?>
</p>
</body>
</html>