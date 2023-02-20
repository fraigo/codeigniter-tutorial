<?php
$config = [
    "id" => @$id,
    "name" => @$name?:'',
    "type" => @$type?:'text',
    "value" => @$value?:'',
];
if (@$readonly) {
    $config["readonly"] = $readonly;
    $config["tabindex"] = -1;
    $config["style"] = "pointer-events: none";
}
if (@$disabled) $config["disabled"] = $disabled;
?>
<?=form_input($config)?>