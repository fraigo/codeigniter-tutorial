<?= $this->extend('layouts/default') ?>
<?= $this->section('content') ?>
    <h2>Users</h2>
    <?php
    helper('html');
    echo htmlTable($items,null,["border"=>1]);
    ?>
<?= $this->endSection() ?>
