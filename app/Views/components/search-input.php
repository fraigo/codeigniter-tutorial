<?php
$config = [];
$config['x-ref'] = "search";
$config["value"] = $value;
?>
<div class="input-group password-view">
<?=form_hidden($name,$value?:'')?>
<?=form_input([
    "id" => "search_description_$name",
    "readonly"=> true,
    "value" => @$options[$value]?:''
])?>
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
            var element = document.querySelector(sel)
            result.push(fld+'='+encodeURIComponent(element.value))
        }
        return result.join('&')
    }
    function openSelect(search, name, extra){
        var extraParameters = extraSelectParameters(extra)
        window.open('/search/select/'+search+'?target='+name+'&'+extraParameters,'search','width=800,height=600')
    }
</script>