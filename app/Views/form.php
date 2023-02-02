<header class="header">
    <div class="container">
    <?php
    helper(['html','form']);
    echo form_open($action, @$formAttributes?:[]);
    ?>
    <div class="d-flex justify-content-between mb-4">
        <h2><?=$title?></h2>
        <div>
            <button type="button" class="btn btn-secondary" onclick="window.history.back(-1)" >Back</button>
            <button type="submit" class="btn btn-primary"><?=@$item["id"]?"Update":"Create"?></button>
        </div>
    </div>
    <?php if (@$success){ ?>
    <div class="alert alert-success p-1 mt-1"><?=$success?></div>
    <?php  } ?>
    <div class="form-content">
    <?php
    foreach($fields as $fld=>$cfg){
        if (@$cfg["header"]){
            echo "<div class=form-header >{$cfg["header"]}</div>";
        }
        $matches = [];
        $match = preg_match("/([a-z0-9_]+)\[([a-z0-9_]+)\]/",$fld, $matches);
        if ($matches){
            $value = @$item[$matches[1]][$matches[2]];
            $error = @$errors[$matches[1]][$matches[2]];
        } else {
            $value = @$item[$fld];
            $error = @$errors[$fld];
        }
        $config = [
            'name'      => $fld,
            'id'        => $fld,
            'errors'    => $error,
            'value'     => set_value($fld,$value,false),
        ];
        $control = @$cfg["control"];
        foreach($cfg as $key=>$val){
            $config[$key] = $val;
        }
        if (is_array(@$cfg["options"])){
            $config['options']   = ([""=>''])+$cfg["options"];
            $config['selected'] = [set_value($fld,$value,false)];
        }
        echo form_item($config, $control);
        
    }
    echo form_close();
?> 
        </div>
    </div>
    <div class="mt-4">
        <?php echo @$details ?>
    </div>
</header>
