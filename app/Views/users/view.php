<?= $this->extend('layouts/default') ?>
<?= $this->section('content') ?>
    <h2>User</h2>
    <?php
    helper('html');
    ?>
    <div class="form-item">
        <label>Id</label>
        <div><?=$item['id']?></div>
    </div>
    <div class="form-item">
        <label>Name</label>
        <div><?=$item['name']?></div>
    </div>
    <div class="form-item">
        <label>Email</label>
        <div><?=$item['email']?></div>
    </div>
    <div class="form-item">
        <label>Last Login</label>
        <div><?=$item['login_at']?></div>
    </div>
    <div class="form-item">
        <label>Last Update</label>
        <div><?=$item['updated_at']?></div>
    </div>
<?= $this->endSection() ?>