<?php

if ($token){
?>
<div id="message" style="display:none">
<h2><?php lang('App.login_successful')?></h2>
<a id="backtoapp" href="<?=$url?>#/login/token/<?=$token?>"><?php lang('App.back_to_app')?></a>
</div>
<?php
} else {
?>
<div id="message" style="display:none">
<h2>Error</h2>
<a id="backtoapp" href="<?=$url?>#/login"><?php lang('App.back_to_app')?></a>
</div>
<?php
}
?>
<script>
    setTimeout(function(){
        document.getElementById('message').style.display='';
    },2000)
    document.getElementById('backtoapp').click();
</script>
