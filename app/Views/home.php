<?= $this->extend('layouts/default') ?>
<?= $this->section('content') ?>
    Main Page Content
    <br>
    Current User: <?php echo @session('auth')['email'] ?>
<?= $this->endSection() ?>

