<?php
$config['x-ref'] = "content";
$config['x-bind:type'] = "visible ? 'text' : 'password'";
?>
<div class="input-group password-view" x-data="{label: 'Show', visible:false}">
<?=form_input($config)?>
<div class="input-group-append" >
    <a class="btn btn-secondary" x-text="visible ? 'Hide' : 'Show'" x-on:click="visible=!visible;">Show</a>
    <a class="btn btn-primary" x-on:click="$refs.content.select();$refs.content.setSelectionRange(0,99999);navigator.clipboard.writeText($refs.content.value);" >Copy</a>
</div>
</div>