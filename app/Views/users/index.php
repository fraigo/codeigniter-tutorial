<?= $this->extend('layouts/default') ?>
<?= $this->section('content') ?>
<header class="header">
    <div class="container-fluid">
    <h2>Users</h2>
    <?php
    helper('html');
    echo $pager->links($pager_group); // $pager->simpleLinks($pager_group);
    echo htmlTable($items,$columns,["width"=>"100%","class"=>"table table-striped table-sm"]);
    ?>
    </div>
</header>
<?= $this->endSection() ?>
