<?php

if (getenv("CUSTOM_URL_SCHEME")){
    $url = str_replace("https:",getenv("CUSTOM_URL_SCHEME"),$url);
}

?>
<div id="message" style="display:none">
<h2>Login Successful</h2>
<a id="backtoapp" href="<?=$url?>#/login/token/<?=$token?>">Go Back to App</a>
</div>
<script>
    setTimeout(function(){
        document.getElementById('message').style.display='';
    },2000)
    document.getElementById('backtoapp').click();
</script>
