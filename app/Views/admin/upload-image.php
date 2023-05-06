<?php
helper('form');
?>
<header class="header">
    <div class="container">
        <h2>Upload Image</h2>
        <form method=POST id=sqlform enctype="multipart/form-data">
        <div class="form-item">
            <label>File</label>
            <?php echo form_input([
                "name" => "file",
                "type" => "file",
                "onchange" => "updatedFile(this)"
            ]); ?>
        </div>
        <div class="form-item">
            <label>Path</label>
            <?php echo form_input([
                "name" => "path",
                "type" => "text",
                "onchange" => "updateName(this)",
                "value" => "public/img",
            ]); ?>
        </div>
        <div class="form-item">
            <label>Override Name</label>
            <?php echo form_input([
                "name" => "name",
                "type" => "text",
                "value" => "",
                "onchange" => "updateName(this)"
            ]); ?>
        </div>
        <div class="form-item">
            <label>Previews</label>
            <div>
                <div style="width:50%">
                    New File:<br>
                    <img style="width: 100%" src="" id="source" onload="this.style.display=''" onerror="this.style.display='none'" >
                </div>
                <div style="width:50%">
                    Current File:<br>
                    <img style="width: 100%" src="" id="target" onload="this.style.display=''" onerror="this.style.display='none'" >
                </div>
            </div>
        </div>
        <div class="form-item">
            <label></label>
            <?php echo form_input([
                "value" => "Submit",
                "type" => "submit",
            ]); ?>
        </div>
        
        </form>
        <script>
            function updatedFile(obj){
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
