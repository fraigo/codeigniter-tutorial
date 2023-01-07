<?= $this->extend('layouts/default') ?>
<?= $this->section('content') ?>
    <h2>Users</h2>
    <?php
    helper('html');
    echo $pager->links($pager_group); // $pager->simpleLinks($pager_group);
    echo htmlTable($items,$columns,["border"=>1,"width"=>"100%"]);
    ?>
<?= $this->endSection() ?>
