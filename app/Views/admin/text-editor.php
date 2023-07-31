<?php
helper('form');

if (@$_POST['content']){
    file_put_contents(ROOTPATH.$filename,$_POST['content']);
}
?>
<header class="header">
    <div class="container">
        <h2>File Editor</h2>
        <form method=POST id=editor onsubmit="return saveFile(this)" enctype="multipart/form-data">
        <div class="form-item">
            <?php echo form_textarea([
                "name" => "content",
                "value" => @file_get_contents(ROOTPATH.$filename),
            ]); ?>
        </div>
        <div class="form-item">
            <?php echo form_input([
                "value" => "Submit",
                "type" => "submit",
            ]); ?>
        </div>
        
        </form>
        <script>
            function saveFile(obj){
                var frm = obj.form
                var file = frm.file.files[0]
                frm.name.value=file.name
                var src = document.getElementById("source")
                console.log('Reading file')
                var fileReader = new FileReader();
                fileReader.readAsDataURL(file);
                fileReader.addEventListener("load", function () {
                    console.log('Done')
                    src.src = this.result
                }); 
                updateName(obj)
            }
            function updateName(obj){
                var frm = obj.form
                var tgt = document.getElementById("target")
                var path = frm.path.value
                var name = frm.name.value
                console.log('name',path,name)
                if (path.indexOf('public/')==0){
                    console.log('load',name)
                    tgt.src = (path.replace('public/','/')+'/'+name).replace('//','/')
                }
            }
        </script>
    </div>
</header>
