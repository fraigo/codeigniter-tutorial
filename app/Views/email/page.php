<?php

$content = @$page["contents"]?:"";

$content = str_replace('[url]','<a href="{url}" style="text-decoration:none !important;"><span style="padding:8px 20px;background-color:#d28e19;color:#f0f0f0;text-decoration:none;">',$content);
$content = str_replace('[/url]','</span></a>',$content);

$content = str_replace("{name}",@$name,$content);
$content = str_replace("{url}",@$url,$content);
$matches = [];
preg_match_all('/src="([^"]+)"/',$content,$matches);
$images = @$matches[1]?:[];
foreach($images as $image){
    $url = imageAttachment($image)['url'];
    $content = str_replace("$image",$url,$content);
}
if (@$replacements){
    foreach($replacements as $rep=>$value){
        if (is_array($value)){
            continue;
        }
        if (!$value){
            continue;
        }
        $content = str_replace("{".$rep."}",$value,$content);
    }
}

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
<?=$content?>
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