<header class="header">
    <div class="container">
        <div class="d-flex justify-content-between mb-4">
            <h2><?=$title?></h2>
            <div>
                <?php if (@$backLink) { ?>
                <button type="button" class="btn btn-secondary" onclick="document.location=this.getAttribute('data-href')" data-href="<?=$backLink?>">Back</button>
                <?php } ?>
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
            <?php if (@$config["label"]!==null) { ?>
            <label><?=$config["label"]?></label>
            <?php } ?>
            <div><?php
            $matches = [];
            $match = preg_match("/([a-z0-9_]+)\[([a-z0-9_]+)\]/",$fld, $matches);
            if ($matches){
                $value = @$item[$matches[1]][$matches[2]];
            } else {
                $value = @$item[$fld];
            }
            helper('form');
            if (@$config["options"]){
                if (@$config["multiple"]){
                    $values = explode(',',$value);
                    foreach($values as $idx=>$val){
                        $values[$idx] = @$config["options"][$val];
                    }
                    echo implode(', ',$values);    
                } else {
                    echo @$config["options"][$value];
                }
            } else if (@$config["view_control"]){
                $config["value"] = $value;
                $config["readonly"] = true;
                echo form_control($config["view_control"],$config);
            } else if (@$config["view_component"]){
                $config["value"] = $value;
                $config["readonly"] = true;
                echo form_component($config["view_component"],$config);
            } else if (@$config["value"]){
                echo $config["value"];
            } else if (@$config["type"]=="password"){
                echo "********************";
            } else {
                echo htmlentities("$value");
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
