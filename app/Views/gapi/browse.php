<?php
    helper('gapi');
?>
<?php if ($file) { ?>
    <h3>
        <img src='/img/gapi/folder.svg' align=absmiddle width=32 height=32> 
        <?php if (@$parent) { ?>
        <a href='/api/google/drive/browse/<?=$parent->id?>?format=html'><?=$parent->name?></a> /
        <?php } ?>
        <?=ucfirst($file->name)?>
    </h3>
    <div class="buttons" style="display:flex">
    <?php if ($file->parents) { ?>
        <a class='button' href='/api/google/drive/browse/<?=$file->parents[0]?>?format=html'>⬅ Back to Parent Folder</a>
    <?php } ?>
    <a class='button' href='/api/google/drive/select/<?=$file->id?>?format=html'>🗂 Select this folder</a>
    <div style="flex:1"></div>
    <a class='button' href='/api/google/drive/browse/?format=html'>Go to Root</a>
    <?php if ($selected) { ?>
        <a class='button' href='/api/google/drive/browse/<?=$selected['id']?>?format=html'>Go to Selected Folder</a>
    <?php } ?>
    </div>
<?php } ?>
<h4>Browse folders</h4>
<?php
$type = "application/vnd.google-apps.folder";
$found = 0;
foreach($items as $file){
    if ($type && $file->mimeType!=$type) {
        continue;
    }
    $found++;
?>
    <div class='file-item' mime='<?=$file->mimeType?>'>
        <div style="padding-right: 4px">
            <a href='/api/google/drive/browse/<?=$file->id?>?format=html'>
                <img src='/img/gapi/folder.svg' align=absmiddle width=24 height=24></a>
        </div>
        <div style='flex:1; line-height: 1.3em; max-height:2.6em;overflow:hidden;'>
            <a href='/api/google/drive/browse/<?=$file->id?>?format=html'><?=$file->name?></a>
        </div>
        <div style=float:right ><a class='button' href='/api/google/drive/browse/<?=$file->id?>?format=html'>Browse</a></div>
    </div>
<?php
}
?>
<?php if ($found==0) { ?>
    <i>No Items Found</i>
<?php } ?>
<style>
body{
    font-family: Arial, Helvetica, sans-serif;
}
.file-item{ font-family: arial, sans;display: none; height: 2.6em; vertical-align: middle; padding: 8px 12px; width: 300px; margin: 4px; border: 1px solid #f0f0f0; }
a{color: #222 !important; text-decoration: none; padding: 4px 0; border-radius:3px;}
a.button{
    border: 1px solid #ddd; 
    background-color: #f0f0f0;
    padding: 8px 16px;
}
.file-item[mime='application/vnd.google-apps.folder'] { 
    display: inline-flex;
    align-items: center;
    background-position: 4px center;
}
</style>