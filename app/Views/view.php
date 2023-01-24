<header class="header">
    <div class="container">
        <div class="d-flex justify-content-between mb-4">
            <h2><?=$title?></h2>
            <div>
                <button type="button" class="btn btn-secondary" onclick="window.history.back(-1)" >Back</button>
                <?php if (@$editLink) { ?>
                <button class="btn btn-primary" onclick="document.location=this.getAttribute('data-href')" data-href="<?=$editLink?>">Edit</button>
                <?php } ?>
            </div>
        </div>
        <div class="block-sm mr-auto">
        <?php foreach($fields as $fld=> $config){ 
            if (@$config["header"]){
                echo "<div class=form-header >{$config["header"]}</div>";
            }
        ?>
        <div class="form-item">
            <label><?=$config["label"]?></label>
            <div><?php
            $value = @$item[$fld];
            if (@$config["options"]){
                echo $config["options"][$value];
            } else if (@$config["value"]){
                echo $config["value"];
            } else if (@$config["type"]=="password"){
                echo "********************";
            } else {
                echo $value;
            }
            ?></div>
        </div>
        <?php } ?>
        </div>
    </div>
    <div class="mt-4">
        <?php echo @$details ?>
    </div>
</header>
