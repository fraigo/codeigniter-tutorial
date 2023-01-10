<?php

function form_item($config,$control="form_input"){
    $id = @$config["id"];
    $label = @$config["label"];
    $error = @$config['errors'] ? "<div class='alert alert-danger p-1 mt-1'>".htmlentities(@$config['errors'])."</div>" : '';
    $errors = "<div class=\"form-error\">$error</div>";
    $functions = [
        "form_input"
    ];
    if (!@$functions[$control]){
        $control = $functions[0];
    }
    $config["class"]="form-control";
    if (@$config["type"]=="submit"){
        $config["class"]="btn btn-default";
    }
    $control = $control($config);
    $label = @$config["label"]!==null ? "<label for=\"{$id}\" >{$label}</label>" : '';
    $template = "<div class=\"form-item\">
    $label
    <div>
        $control
        $errors
    </div>
</div>";
    return $template;
}

function form_errors($errors=null){
    if (!$errors) return null;
    return "<div class=\"form-errors alert alert-danger p-1 mt-1\">".implode("<br>",$errors)."</div>";
}