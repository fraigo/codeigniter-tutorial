<?php

?>
<div id="message" style="display:none">
<h2><?php echo $title ?></h2>
<p><?php echo $message ?></p>
<a id="backtoapp" href="<?=$url?>#/login">Go Back to App</a>
</div>
<script>
    setTimeout(function(){
        document.getElementById('message').style.display='';
    },2000)
    setTimeout(function(){
        document.getElementById('backtoapp').click();
    },5000)
</script>
