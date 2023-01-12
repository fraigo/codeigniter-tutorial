<?php

$theme = "sync";
define("THEME",$theme);

function theme_url($url){
    $theme = THEME;
    return site_url("themes/$theme/$url");
}

function theme_view($view){
    $theme = THEME;
    return "themes/$theme/$view";
}

if (file_exists("/../Views/themes/$theme/helper.php")){
    include(__DIR__ . "/../Views/themes/$theme/helper.php");
}
