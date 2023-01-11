<?php

function form_item($config,$control="form_input",$class="form-item"){
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
    $template = "<div class=\"$class\">
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

function form_filters($filters=[],$title="Filters"){
    if (!$filters) return null;
    $content = [];
    $content[] = form_open(current_url(),["method"=>"GET","class"=>"form-filters d-flex flex-wrap"]);
    $content[] = "<b class=\"col-12 mt-2\">$title</b>";
    $inputs = [];
    foreach($filters as $field=>$cfg){
        $inputs[] = $cfg["name"];
        $content[] = form_item($cfg,"form_input","form-item col-12 col-sm-6 col-lg-4");
    }
    foreach($_GET as $fld=>$value){
        if (!in_array("$fld",$inputs)){
            $content[] = form_hidden($fld,$value);
        }
    }
    $content[] = '<input type="submit" hidden="true" />';
    $content[] = form_close();
    return implode("\n",$content);
}