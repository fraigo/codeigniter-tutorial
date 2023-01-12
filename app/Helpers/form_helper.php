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
    if (@$config["type"]=="submit"){
        @$config["class"] .= " btn";
    } else {
        @$config["class"] .= " form-control";
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
    $content[] = form_open(current_url(),["method"=>"GET","class"=>"form-filters d-flex flex-column flex-wrap mb-2"]);
    $toggle = '<button class="btn btn-primary btn-sm" type="button" data-toggle="collapse" data-target=".filterCollapse" aria-expanded="false" aria-controls="filterCollapse">Show/Hide</span></button>';
    $content[] = '<div class="d-flex align-items-center p-2 justify-content-between"><b class=\"col-12 mt-2\">'.$title.'</b>'.$toggle.'</div>';
    $content[] = '<div class="collapse filterCollapse" ><div class="d-flex flex-wrap">';
    $inputs = [];
    foreach($filters as $field=>$cfg){
        $inputs[] = $cfg["name"];
        $content[] = form_item($cfg,"form_input","form-item col-12 col-md-6 col-lg-4");
    }
    foreach($_GET as $fld=>$value){
        if (!in_array("$fld",$inputs)){
            $content[] = form_hidden($fld,$value);
        }
    }
    $content[] = '</div></div>';
    $content[] = '<input type="submit" hidden="true" />';
    $content[] = form_close();
    return implode("\n",$content);
}