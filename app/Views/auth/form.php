<?= $this->extend('layouts/default') ?>
<?= $this->section('content') ?>
    <h2>Login</h2>
    <form action="/auth/login" method="POST">
        <div><label>Email</label><br><input type="text" name="email" value=""></div>
        <div><label>Password</label><br><input type="password" name="password" value=""></div>
        <div><input type="submit" value="Log In"></div>
        <div><?= implode('<br>',$errors?:[]) ?></div>
    </form>
<?= $this->endSection() ?>
