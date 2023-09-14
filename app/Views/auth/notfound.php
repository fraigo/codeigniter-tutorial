<?php

?>
<div id="message" style="display:none">
<h2>Email is not registered</h2>
<p>
    <?php echo $email ?> is not registered in the app.<br>
    Please log in using the correct email or try log in using a different method.
</p>
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
