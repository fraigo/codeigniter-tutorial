<?php
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Password Recovery</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>
<body>
<table bgcolor="#eeeeee" width="100%" style="width: 100%">
    <tr>
        <td></td>
        <td width="600">
        <table bgcolor="#f8f8f8" width="600">
            <tr>
                <td width="20"></td>
                <td>
<div>&nbsp;</div>
Hello <?=$name?>,<br>
<div>&nbsp;</div>
We received a password reset request for your account.<br>
<div>&nbsp;</div>
You can reset your password by clicking the link below:<br>
<div>&nbsp;</div>
<a href="<?=$url?>"><?=$url?></a>
<div>&nbsp;</div>
<b>Admin Team</b><br>
<?=base_url()?>
<div>&nbsp;</div>
                </td>
                <td width="20"></td>
            </tr>
        </table>
        </td>
        <td></td>
    </tr>
</table>
</body>
</html>