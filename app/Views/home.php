<?= $this->extend('layouts/default') ?>
<?= $this->section('content') ?>
<header>
    <div class="container">
    Main Page Content
    <br>
    Current User: <?php echo @session('auth')['email'] ?>
    </div>
</header>
<?= $this->endSection() ?>

