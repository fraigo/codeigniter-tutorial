<?php
 $value = $value ?: @$default_image ?: '';
 $tmp_id = $id ?: "tmp_$name";
 $width = @$width ?: 64;
 $height = @$height ?: 64;
 $contain = @$contain ? 1 : 0;
?>
<div class="image-upload form-input">
    <?=form_component('avatar',[
        'id'=>"$tmp_id",
        'name'=>"$name",
        'value'=>"$value",
        'width'=>$width,
        'height'=>$height,
        'contain'=>$contain,
        'default_image'=>@$default_image,
        'onclick'=>"document.getElementById('upload_$tmp_id').click()"
    ])?>
    <input id="upload_<?=$tmp_id?>" type="file" accept="image/png,image/jpeg" style="display:none"
        onchange="updatePreview(this,'canvas_<?=$tmp_id?>','<?=$tmp_id?>','avatar_<?=$tmp_id?>',<?=$width*2?>,<?=$height*2?>,<?=$contain?>)" >
    <canvas id="canvas_<?=$tmp_id?>" width="<?=$width*2?>" height="<?=$height*2?>" style="display:none">
</div>
<script>
    function updatePreview(fileInput,canvasId,inputId,avatarId, width, height, contain){
            
            var files = fileInput.files
            var file = files[0]
            if (file){
                var reader = new FileReader();
                reader.onload = (e) => {
                    var canvas = document.getElementById(canvasId)
                    var img = new Image()
                    img.onload = () => {
                        var side = Math.min(img.naturalWidth, img.naturalHeight)
                        var sx = side
                        var sy = side
                        var dx = (img.naturalWidth - side) / 2
                        var dy = (img.naturalHeight - side) / 2
                        var finalWidth = width
                        var finalHeight = height
                        if (contain) {
                            console.log('rate',width/height,img.naturalWidth/img.naturalHeight)
                            if (width/height>img.naturalWidth/img.naturalHeight){
                                sx = img.naturalHeight * width / height
                                sy = img.naturalHeight
                                dx = (img.naturalWidth - sx) / 2
                                dy = (img.naturalHeight - sy) / 2
                            } else {
                                sx = img.naturalWidth
                                sy = img.naturalWidth * height / width
                                dx = (img.naturalWidth - sx) / 2
                                dy = (img.naturalHeight - sy) / 2
                            }
                        }
                        if (contain){
                            sx = img.naturalWidth
                            sy = img.naturalHeight
                            dx = 0
                            dy = 0
                            finalWidth = width
                            finalHeight = width * img.naturalHeight / img.naturalWidth
                        }
                        canvas.setAttribute('width',finalWidth)
                        canvas.setAttribute('height',finalHeight)
                        var ctx = canvas.getContext('2d')
                        ctx.clearRect(0, 0, width, height)
                        ctx.drawImage(img, dx, dy, sx, sy, 0, 0, finalWidth, finalHeight)
                        var input = document.getElementById(inputId)
                        input.value = canvas.toDataURL()
                        var avatar = document.getElementById(avatarId)
                        avatar.style.backgroundImage = "url('" + canvas.toDataURL() + "')"
                    }
                    img.src = e.target.result
                    
                }
                reader.readAsDataURL(file);
            }
        }
</script>