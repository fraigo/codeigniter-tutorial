<?php
$config = [
    "id" => "search_description_$name",
    "type" => @$type?:'text',
    "value" => @$options[$value]?:$value,
];
if (!@$editable){
    $config["readonly"] =  true;
}
?>
<div class="input-group search-input">
<?=form_hidden($name,$value?:'')?>
<?=form_input($config)?>
<?php if (!@$readonly){ ?>
<div class="input-group-append" >
    <a class="btn btn-primary" data-search="<?=$search?>" data-extra="<?=@$search_values?>" data-name="<?=$name?>" onclick="openSelect(this.getAttribute('data-search'),this.getAttribute('data-name'),this.getAttribute('data-extra'))" >Select</a>
</div>
<?php } ?>
</div>
<script>
    function extraSelectParameters(extra){
        if (!extra) return '';
        var items = extra.split(',')
        var result = [];
        for(var idx in items){
            var parts = items[idx].split('=')
            var fld = parts[0]
            var sel = parts[1]
            var value = sel
            try {
                var element = document.querySelector(sel)
                if (element) {
                    value = element.value
                }
            } catch(e){
            }
            result.push(fld+'='+encodeURIComponent(value))
        }
        return result.join('&')
    }
    function openSelect(search, name, extra){
        var extraParameters = extraSelectParameters(extra)
        window.open('/search/select/'+search+'?target='+name+'&'+extraParameters,'search','width=800,height=600')
    }
</script>