<?php

if ($token){
?>
<div id="message" style="display:none">
<h2>Login Successful</h2>
<a id="backtoapp" href="<?=$url?>#/login/token/<?=$token?>">Go Back to App</a>
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
