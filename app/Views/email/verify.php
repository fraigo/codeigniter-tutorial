<?php

?><!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Email Verification</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>
<body>
<table bgcolor="#eeeeee" width="100%" style="width: 100%">
<tbody>
    <tr>
        <td>&nbsp;</td>
        <td width="600">
        <table bgcolor="#f8f8f8" width="600">
            <tr height="20">
                <td width="20"></td>
                <td align="center">
                    <div style="height:20px">&nbsp;</div>
                </td>
                <td width="20"></td>
            </tr>
            <tr height="100">
                <td width="20"></td>
                <td align="center">
<img src="<?=imageAttachment(getenv('app.logo'))['url']?>" alt="Logo" height="92">
                </td>
                <td width="20"></td>
            </tr>
            <tr>
                <td width="20"></td>
                <td>
<div>&nbsp;</div>
Hello <?=$name?>,<br>
<div>&nbsp;</div>
Please verify your email for <?php echo getenv('app.name') ?> by clicking the link below:<br>
<div>&nbsp;</div>
<a href="<?=$url?>">
<?=$url?>
</a>
<div>&nbsp;</div>
<?=@$sender?:'<b>'.getenv('app.name').'</b>'?></b><br>
<div>&nbsp;</div>
<?=@$footer?>
                </td>
                <td width="20"></td>
            </tr>
        </table>
        </td>
        <td>&nbsp;</td>
    </tr>
<tbody>
</table>
</body>
</html>