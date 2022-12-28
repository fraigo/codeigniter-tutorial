<?php

function form_item($config,$control="form_input"){
    $id = @$config["id"];
    $label = @$config["label"];
    $error = htmlentities(@$config['errors']?:'');
    $errors = "<div class=\"form-error\">$error</div>";
    $functions = [
        "form_input"
    ];
    if (!@$functions[$control]){
        $control = $functions[0];
    }
    $control = $control($config);
    $template = "<div class=\"form-item\">
    <label for=\"{$id}\" >{$label}</label>
    <div>
        $control
        $errors
    </div>
</div>";
    return $template;
}

function form_errors($errors=null){
    if (!$errors) return null;
    return "<div class=\"form-errors\">".implode("<br>",$errors)."</div>";
}