<?= $this->extend('layouts/default') ?>
<?= $this->section('head') ?>
<link rel="stylesheet" href="/css/style.css" >
<?= $this->endSection() ?>
<?= $this->section('header') ?>
<div class="d-flex flex-column flex-md-row align-items-sm-center p-3 px-md-4 mb-3 bg-white border-bottom box-shadow">
      <h5 class="my-0 mr-md-auto font-weight-normal" style="line-height:2em;max-width: 200px;">My Web Application</h5>
      <div class="d-flex justify-content-center align-items-center">
      <nav class="my-2 my-md-0 mr-md-3">
        <a  class="p-2 text-dark" href="/" >Home</a>
        <?php if(session('admin')) { ?>
            <a class="p-2 text-dark" href="/users/" >Users</a>
            <a class="p-2 text-dark" href="/usertypes/" >User Types</a>
        <?php } ?>
      </nav>
      <?php if(session('auth')) { ?>
        <a class="btn menu-login btn-outline-primary" href="/auth/logout">Log Out</a>
      <?php } else { ?>
        <a class="btn menu-login btn-outline-primary" href="/auth/login">Log In</a>
      <?php } ?>
      </div>
</div>
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<?= $content ?>
<?= $this->endSection() ?>