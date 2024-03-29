<?php

function form_component($name, $config){
    return view("components/$name", $config, ['saveData'=>false]);
}

function form_control($control,$config){
    $id = @$config["id"];
    unset($config["hidden"]);
    if ($control==null && @$config["options"]){
        $control = "form_dropdown";
    }
    if (@$config["disabled"]===null){
        unset($config["disabled"]);
    }
    if (@$config["readonly"]===null){
        unset($config["readonly"]);
    }
    
    if ($control==null && @$config["options"]){
        $control = "form_dropdown";
    }
    $functions = [
        "form_input",
        "form_dropdown",
        "form_textarea"
    ];
    if (!in_array($control,$functions)){
        $control = $functions[0];
    }
    if ($control=="form_input"){
        foreach($config as $key=>$cfg){
            if (is_array($cfg)){
                unset($config[$key]);
            }
        }
    }
    if (@$config["type"]=="submit"){
        @$config["class"] .= " btn";
    } else {
        @$config["class"] .= " form-control";
    }
    return $control($config);
}

function form_item($config,$control="form_input",$class="form-item"){
    $id = @$config["id"];
    $label = @$config["label"];
    //unset($config["label"]);
    $error = @$config['errors'] ? "<div class='alert alert-danger p-1 mt-1'>".htmlentities(@$config['errors'])."</div>" : '';
    $errors = "<div class=\"form-error\">$error</div>";
    if (@$config["component"]){
        if ($config["component_options"]){
            foreach($config["component_options"] as $opt=>$value){
                $config[$opt]=$value;
            }
        }
        $controlContent = form_component($config["component"], $config);
    } else {
        $controlContent = form_control($control,$config);
    
    }
    $labelContent = @$config["label"]!==null ? "<label for=\"{$id}\" >{$label}</label>" : '';
    $template = "<div class=\"$class\">
    $labelContent
    <div>
        $controlContent
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
    $clearUrl = current_url(true);
    foreach($filters as $fld=>$cfg){
        $clearUrl->stripQuery($cfg['name']);
    }
    $content = [];
    $id = rand(10000,99999);
    $content[] = form_open(current_url(),["method"=>"GET","class"=>"form-filters d-flex flex-column flex-wrap mb-2"]);
    $clear = '<input type="button" class="btn btn-secondary btn-sm" type="button" data-href="'.$clearUrl.'" onclick="document.location=this.getAttribute(\'data-href\')" value="Clear">';
    $toggle = '<button class="btn btn-primary btn-sm" type="button" data-toggle="collapse" data-target="#collapse_'.$id.'" aria-expanded="false" aria-controls="filterCollapse">Show/Hide</span></button>';
    $desc = '';
    foreach($filters as $field=>$cfg){
        if (@$cfg["hidden"]) continue;
        $value = null;
        if (@$cfg["value"]!==null && @$cfg["value"]!==''){
            $value = $cfg["value"];
            if (@$cfg["options"]){
                $value = @$cfg["options"][$value];
            }
        }
        if ($value!=''){
            $desc .= "<span class='ml-2 p-1 px-2 badge badge-secondary'>{$cfg['label']}:{$value}</span>";
        }
    }
    $content[] = '<div class="d-flex align-items-center p-2 justify-content-between"><div><b class=\"mt-2\">'.$title.'</b>'.$desc.'</div><div>'.$clear." ".$toggle.'</div></div>';
    $content[] = '<div class="collapse filterCollapse" id="collapse_'.$id.'"><div class="d-flex flex-wrap">';
    $inputs = [];
    foreach($filters as $field=>$cfg){
        if (@$cfg["hidden"]) continue;
        $inputs[] = $cfg["name"];
        $control = @$cfg["control"]?:"form_input";
        $selvalue = @$cfg["options"] ? $cfg["options"][@$cfg['value']] : null;
        $item = [
            "name" => @$cfg["name"],
            "value" => @$cfg["value"],
            "label" => @$cfg["label"],
            "options" => @$cfg["options"],
            "selected" => @$cfg["selected"],
            "onreset" => "this.value=''",
            "onchange" => "this.form.submit()",
        ];        
        $script = '';
        if ($selvalue && $cfg['value']=="0"){
            $script = "<script>document.querySelector(\"select[name='{$cfg["name"]}']\").value='{$cfg["value"]}'</script>";
        }
        $content[] = form_item($item,$control,"form-item col-12 col-md-6 col-lg-4") . $script;
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