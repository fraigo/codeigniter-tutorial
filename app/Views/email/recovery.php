<?php

?><!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Password Recovery</title>
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
><?=lang('App.password_recovery_message',[getenv('app.name')])?><br>
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