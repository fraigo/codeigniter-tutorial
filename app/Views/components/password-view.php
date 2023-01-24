
<div class="input-group password-view" >
<?=form_input($config)?>
<div class="input-group-append">
    <a class="btn btn-secondary" onclick="var input=this.parentNode.parentNode.querySelector('input'); if (input.type=='text') { input.type='password'; this.innerText='Show' } else { input.type='text'; this.innerText='Hide' }" >Show</a>
    <a class="btn btn-primary" onclick="var input=this.parentNode.parentNode.querySelector('input');input.select();input.setSelectionRange(0,99999);navigator.clipboard.writeText(input.value);" >Copy</a>
</div>
</div>