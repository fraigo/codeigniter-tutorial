<?php

echo form_hidden($name,$value?:'');
echo form_input([
    "type" => "datetime-local",
    "value" => $value,
    "component" => "datetime-input",
    "onchange" => "this.form.$name.value=this.value.replace('T',' ')+':00'"
])

?>