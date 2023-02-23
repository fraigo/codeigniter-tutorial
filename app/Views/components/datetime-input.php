<div class="input-group search-input">
<?php
$inputid = "datetime_selector_$name";
echo form_hidden($name,$value?:'');
echo form_input([
    "id" => $inputid,
    "type" => "datetime-local",
    "value" => $value,
    "step" => "900",
    "onchange" => "datetime_input_change(this,'$name')"
])
?>
<div class="input-group-append" >
    <a class="btn btn-primary" onclick="datetime_input_date(1,'<?=$name?>','<?=$inputid?>')">Today</a>
</div>
</div>
<script>
    function datetime_input_change(element,name){
        element.form[name].value=element.value.replace('T',' ')+':00'
    }
    function datetime_input_date(hours,name,inputid){
        var date = moment().add(hours,'hours').format('YYYY-MM-DD HH[:00:00]')
        document.querySelector('#'+inputid).value=date;
        document.querySelector("input[name='"+name+"']").value=date;
    }
//# sourceURL=datetime-input.js
</script>