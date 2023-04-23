<?php

$button = email_button(@$link,"View Details");

?><!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title><?=@$title?:"Notification"?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>
<body>
<table bgcolor="#eeeeee" width="100%" style="width: 100%">
<tbody>
    <tr>
        <td>&nbsp;</td>
        <td width="600">
        <table bgcolor="#f8f8f8" width="600">
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
<p>
<?=@$content?>
</p>
<br/>
<p>
<?=@$button?>
</p>
                </td>
                <td width="20"></td>
            </tr>
            <tr>
                <td width="20"></td>
                <td>
<br/>
Sincerely,
<br/>
<b>The Staff Grabs Team</b>
<p>&nbsp;</p>
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