<header class="header">
    <div class="container">
    <?php
    helper(['html','form']);
    echo form_open(isset($item["id"])?"/$route/edit/{$item["id"]}":"/$route/new", []);
    ?>
    <div class="d-flex justify-content-between mb-4">
        <h2><?=$title?></h2>
        <div>
            <button type="button" class="btn" onclick="window.history.back(-1)" >Back</button>
            <button type="submit" class="btn btn-primary"><?=@$item["id"]?"Update":"Create"?></button>
        </div>
    </div>
    <?php
    foreach($fields as $fld=>$cfg){
        if (@$cfg["options"]){
            echo form_item([
                'label'     => $cfg["label"],
                'name'      => $fld,
                'id'        => $fld,
                'options'   => ([""=>''])+$fields[$fld]["options"],
                'selected' => [set_value($fld,@$item[$fld])],
                'errors'    => @$errors[$fld]
            ],"form_dropdown");
        } else {
            echo form_item([
                'label'     => $cfg["label"],
                'type'      => @$cfg["type"],
                'name'      => $fld,
                'id'        => $fld,
                'value'     => set_value($fld,@$item[$fld]),
                'errors'    => @$errors[$fld]
            ]);
        }
    }
?>
    </div>
</header>