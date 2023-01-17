<header class="header">
    <div class="container">
        <div class="d-flex justify-content-between mb-4">
            <h2><?=$title?></h2>
            <div>
                <button type="button" class="btn" onclick="window.history.back(-1)" >Back</button>
                <?php if (module_access($route,2)) { ?>
                <button class="btn btn-primary" onclick="document.location=this.getAttribute('data-href')" data-href="<?="/$route/edit/{$item['id']}"?>">Edit</button>
                <?php } ?>
            </div>
        </div>
        <?php foreach($fields as $fld=> $config){ ?>
        <div class="form-item">
            <label><?=$config["label"]?></label>
            <div><?php
            $value = @$item[$fld];
            if (@$config["options"]){
                echo $config["options"][$value];
            } else {
                echo $value;
            }
            ?></div>
        </div>
        <?php } ?>
        <?php echo @$details ?>
    </div>
</header>
