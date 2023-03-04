<?php
 $value = $value ?: @$default_image;
 $width = @$width ?: 64;
 $height = @$height ?: 64;
 $contain = @$contain ? 1 : 0;
 $class = $contain ? "avatar-rect ".@$class : @$class;
?>
<input id="<?=@$id?>" type="hidden" name="<?=@$name?>"  >
<div id="avatar_<?=@$id?>" class="avatar <?=$class?>" 
    onclick="<?=htmlentities(@$onclick?:'')?>" value="<?=@$value?>"  
    style="background-image:url('<?=$value?>');width:<?=$width?:128?>px;height:<?=$height?:128?>px">
</div>
<style>
    .avatar{
        display: inline-block;
        border-radius: 99% 99%;
        border: 1px solid #eee;
        background-repeat: no-repeat;
        background-size: cover;
        background-position: center;
    }
    .avatar-rect{
        border-radius: 5px;
        background-size: contain;
    }
    .avatar-bg{
        background-color: rgba(128,128,128,0.5);
    }
</style>