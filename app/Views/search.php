<?= $this->extend('layouts/default') ?>
<?= $this->section('head') ?>
<link rel="stylesheet" href="/css/style.css" >
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<?= $content ?>
<script>
    function selectItemFromSearch(e,name){
        console.log('select',e,name);
        var id = e.getAttribute('data-id')
        var desc = e.getAttribute('data-description')
        var targetId = window.opener.document.querySelector("input[name='"+name+"']");
        if (targetId){
            targetId.value = id
        }
        var targetDesc = window.opener.document.querySelector("input[id='search_description_"+name+"']");
        if (targetDesc){
            targetDesc.value = desc
        }
        if (targetId){
            self.close()
        }
    }
</script>
<style>
    h2{
        display: none;
    }
</style>
<?= $this->endSection() ?>
