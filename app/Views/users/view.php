<?= $this->extend('layouts/default') ?>
<?= $this->section('content') ?>
<header class="header">
    <div class="container">
    <div class="d-flex justify-content-between mb-4">
        <h2>View User</h2>
        <div>
            <button type="button" class="btn" onclick="document.location=this.getAttribute('data-href')" data-href="/users/">Back</button>
            <button class="btn btn-primary" onclick="document.location=this.getAttribute('data-href')" data-href="/users/edit/<?=$item['id']?>">Edit</button>
        </div>
    </div>
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
    </div>
</header>
<?= $this->endSection() ?>